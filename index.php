<?php
set_time_limit( 5 * 60 );// unit is seconds
require_once './functions.php';

if ( session_status() !== PHP_SESSION_ACTIVE ) {
    session_start();
}
if ( !isset( $_SESSION['app_options'] ) ) {
    // Load the application configuration
    $config_file = dirname( __FILE__ ) .DIRECTORY_SEPARATOR. '.config';
    if ( file_exists( $config_file ) ) {
        $_conf = json_decode( @file_get_contents( $config_file ), true );
        if ( !empty( $_conf ) ) {
            $opts = filter_config( $_conf );
            // Session initialization
            $_SESSION['app_options'] = $opts;
            session_commit();
            logger( 'Session had been initialized.', $opts );
        } else {
            die( 'Invalid configuration.' );
        }
    } else {
        die( 'This application configuration not found.' );
    }
} else {
    $opts = $_SESSION['app_options'];
    logger( 'Set application settings got from session.' );
}

if ( !isset( $opts['html']['lang'] ) || empty( $opts['html']['lang'] ) || $opts['html']['lang'] === 'auto' ) {
    $opts['html']['lang'] = ( $http_langs = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? explode( ',', $http_langs )[0] : 'en';
}
// Load the translate file
$translate_file = sprintf( '%s%s%s.json', $opts['lang_dir'], DIRECTORY_SEPARATOR, $opts['html']['lang'] );
if ( file_exists( $translate_file ) ) {
    $opts['translations'] = json_decode( @file_get_contents( $translate_file ), true ) ?: [];
} else {
    $opts['translations'] = [];
}

$details    = [];
$target_url = '';
$action     = 'get_details';// To default
if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['action'] ) ) {
    // For POST request
    $action        = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
    $title         = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_STRING );
    $url           = filter_input( INPUT_POST, 'url', FILTER_SANITIZE_URL );
    $playlist_item = filter_input( INPUT_POST, 'playlist_item', FILTER_SANITIZE_URL );
    $analyze_type  = filter_input( INPUT_POST, 'analyze_type', FILTER_SANITIZE_STRING );
    $extension     = filter_input( INPUT_POST, 'extension', FILTER_SANITIZE_STRING );
    $format_code   = filter_input( INPUT_POST, 'format_code', FILTER_SANITIZE_STRING );
    $output_format = filter_input( INPUT_POST, 'output_format', FILTER_SANITIZE_STRING );
    $audio_quality = filter_input( INPUT_POST, 'audio_quality', FILTER_VALIDATE_INT );
    switch ( $action ) {
        case 'get_details':
            if ( isset( $url ) && !empty( $url ) ) {
                $target_url = filter_url( $url );
                $params = get_url_params( $target_url );
                list( $base_url, ) = explode( '?', $target_url );
                logger( $_POST, $analyze_type, $target_url, $params, $base_url );
                if ( array_key_exists( 'list', $params ) && 'playlist' === $analyze_type ) {
                    // Retrieve playlist
                    $target_url = $base_url .'?'. http_build_query( [ 'list' => $params['list'] ] );
                    $res = exec_dl( [ '--flat-playlist -j', $target_url ] );
                    if ( $res['status'] ) {
                        $details['playlist'] = array_map( fn( $v ) => json_decode( $v, true ), $res['response'] );
                        $action  = 'playlist';
                    } else {
                        $res['message'] = 'Failed to retrieve play-list data.';
                    }
                } else {
                    // Retrieve one media
                    if ( isset( $playlist_item ) && !empty( $playlist_item ) ) {
                        $target_url = $playlist_item;
                    } elseif ( isset( $params['v'] ) ) {
                        $target_url = $base_url .'?'. http_build_query( [ 'v' => $params['v'] ] );
                    }
                    logger( 'Get a media', $target_url );
                    $res = exec_dl( [ '-F', $target_url ] );
                    if ( $res['status'] ) {
                        $details['format'] = get_format_list( $res['response'] );
                        $info = exec_dl( [ '--get-title --get-id --get-thumbnail --get-description', $target_url ] );
                        if ( $info['status'] ) {
                            $_data = array_map( fn( $v ) => mb_convert_encoding( $v, 'utf-8', 'utf-8,sjis-win' ), $info['response'] );
                            $details['info'] = [
                                'title'       => $_data[0],
                                'id'          => $_data[1],
                                'thumbnail'   => $_data[2],
                                'description' => implode( "<br>", array_slice( $_data, 3 ) ),
                            ];
                        }
                        $action = 'download';
                    } else {
                        $res['message'] = 'Failed to retrieve detailed data.';
                    }
                }
            } else {
                header( 'Location: ' . dirname( $_SERVER['PHP_SELF'] ) );
            }
            break;
        case 'download':
            if ( isset( $format_code ) && isset( $url ) && !empty( $url ) ) {
                // Default command for youtube-dl
                // cf. `youtube-dl -f {format_code} -o "{dest_path}/%(title)s-%(id)s.%(ext)s" {url}`
                $options = [
                    '-f'  => $format_code,
                    '-o'  => '"'. $opts['dist_base'] .DIRECTORY_SEPARATOR. '%(title)s-%(id)s.%(ext)s"',
                    'url' => filter_url( $url ),
                ];
                if ( !empty( $output_format ) && $extension !== $output_format ) {
                    if ( $opts['dl_with_conv'] ) {
                        // Use convert option on downloader
                        // cf. `youtube-dl -f {format_code} -o "{dest_path}/%(title)s-%(id)s.mp3" -x --audio-format mp3 --audio-quality 0 {url}`
                        $options['-x'] = [ '--audio-format ' . $output_format ];
                        $options['-o'] = '"'. $opts['dist_base'] .DIRECTORY_SEPARATOR. '%(title)s-%(id)s.'. $output_format .'"';
                        if ( in_array( $output_format, [ 'm4a', 'mp3', 'wav' ], true ) ) {
                            // Use audio quality option
                            $_ql = abs( $audio_quality - 9 );
                            $options['-x'][] = '--audio-quality ' . $_ql;
                        }
                        // Merge -x options
                        $options['-x'] = implode( ' ', $options['-x'] );
                    } else {
                        $options['-o'] = '"'. $opts['dist_base'] .DIRECTORY_SEPARATOR. '%(id)s.%(ext)s"';
                    }
                }
                $res = exec_dl( $options );
                if ( $res['status'] ) {
                    if ( !empty( $output_format ) && $extension !== $output_format && !$opts['dl_with_conv'] ) {
                        // Convert after download
                        if ( isset( $title ) && !empty( $title ) ) {
                            $title = filter_dest_title( $title );
                            $title .= '-'. $res['destination']['name'];
                        } else {
                            $title = $res['destination']['name'];
                        }
                        $options = [
                            '-i'   => '"'. $res['destination']['path'] .'"',
                            '-ar'  => 44100,
                            '-ab'  => '128k',
                            'dest' => '"'. $opts['dist_base'] .DIRECTORY_SEPARATOR. $title .'.'. $output_format .'"',
                        ];
                        $res_conv = edit( $options );
                        logger( $options, $res_conv );
                        if ( $res_conv['status'] ) {
                            $res['message'] = 'Successfully downloaded then had been converted format!';
                        } else {
                            $res['status']  = false;
                            $res['message'] = 'Downloaded, but failure to convert format.';
                        }
                    } else {
                        // Completed downloading only
                        $res['message'] = 'Successfully downloaded!';
                    }
                } else {
                    $res['message'] = 'Failed to download file.';
                }
            } else {
                $res = [ 'status' => false, 'message' => 'Parameters are missing for download.' ];
            }
            $action = 'get_details';
            break;
        default:
            $res = [ 'status' => false, 'message' => 'Invalid access.' ];
            break;            
    }
} else {
    // For GET request
    $action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
    switch ( $action ) {
        case 'initialize':
            $_SESSION = [];
            header( 'Location: /' );
            exit;
    }
    $action = 'get_details';// Back to default
    $res = [ 'status' => true, 'message' => null ];
}

include './view.php';