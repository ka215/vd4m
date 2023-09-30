<?php
// Functions

function filter_config( array $config ): array {
    $settings = [];
    foreach ( $config as $_key => $_value ) {
        switch ( $_key ) {
            case 'downloader':
            case 'editor':
                $is_path_string = count( explode( DIRECTORY_SEPARATOR, $_value ) ) > 1;
                $app_fullpath = $is_path_string ? $_value : search_app( $_value );
                $_cmd = "{$app_fullpath} --version";
                exec( $_cmd, $_a, $_r );
                $is_standby = $_r == 0;
                if ( !$is_standby ) {
                    $_cmd = "{$app_fullpath} -version";
                    exec( $_cmd, $_a, $_r );
                    $is_standby = $_r == 0;
                }
                $settings[$_key] = $app_fullpath;
                if ( $is_standby ) {
                    $settings[$_key . '_name'] = basename( $app_fullpath );
                    $settings[$_key . '_version'] = array_shift( $_a );
                }
                break;
            case 'dist_base':
            case 'lang_dir':
                $settings[$_key] = str_replace( '%current_dir%', dirname( __FILE__ ), $_value );
                break;
            default:
                $settings[$_key] = $_value;
                break;
        }
    }
    return $settings;
}

function search_app( string $app_name ): ?string {
    $app_path = null;
    exec( "where {$app_name}", $_a, $_r );
    if ( $_r == 0 && !empty( $_a ) ) {
        $app_path = str_replace( '.exe', '', $_a[0] );
    }
    return $app_path; 
}

function is_vector( array $arr ): bool {
    return array_values( $arr ) === $arr;
}

function filter_url( string $url_string ): string {
    return preg_replace( '/\Ahttps?:\/\//', '', $url_string );
}

function filter_dest_title( string $title ): string {
    $title_raw = $title;
    $special_chars = [ '?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"', '&', '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}', '%', '+', '’', '«', '»', '”', '“', chr( 0 ) ];
    $title = strip_tags( $title );
    $title = str_replace( $special_chars, '', $title );
    $title = str_replace( [ '%20', '+' ], ' ', $title );
    $title = preg_replace( '/[\r\n\t -]+/', '_', $title );
    $title = trim( $title, '.-_' );
    if ( $title_raw !== $title ) {
        logger( __FUNCTION__, $title_raw, $title );
    }
    return $title;
}

function get_url_params( string $url_string ): array {
    $param_str = parse_url( $url_string, PHP_URL_QUERY );
    $param_str = html_entity_decode( $param_str );
    $param_arr = explode( '&', $param_str );
    $params    = [];
    foreach ( $param_arr as $item ) {
        if ( strpos( $item, '=' ) !== false ) {
            list( $key, $val ) = explode( '=', $item );
            $params[$key] = $val;
        }
    }
    unset( $item, $key, $val );
    return $params;
}

function get_format_list( array $data ): array {
    $format_list = [];
    foreach ( $data as $_line ) {
        if ( preg_match( '/\A^(?P<format_code>\d+)\s+(?P<extension>\w+)\s+(?P<resolution_note>.+)\Z/', $_line, $matches ) && $matches[1] ) {
            $format_list[] = [
                'format_code'     => (int)$matches['format_code'],
                'extension'       => $matches['extension'],
                'resolution_note' => $matches['resolution_note'],
            ];
        }
    }
    return $format_list;
}

function exec_dl( $options ) {
    global $opts;
    if ( !is_array( $options ) ) {
        $option_str = (string)$options;
    } elseif ( is_vector( $options ) ) {
        $option_str = implode( ' ', $options );
    } else {
        $target_url = $options['url'];
        unset( $options['url'] );
        array_walk( $options, function( string $_v, string $_k ) use( &$option_str ) { $option_str .= $_k .' '. $_v .' '; } );
        $option_str .= ' ' . $target_url;
    }
    $cmd = sprintf( '%s %s', $opts['downloader'], $option_str );
    exec( $cmd, $verbose, $result );
    $dest_file_path = null;
    $dest_file_name = null;
    if ( !empty( $verbose ) && is_array( $verbose ) ) {
        $_items = array_filter( $verbose, function( $_v ) { return preg_match( '/\sDestination\:\s/', $_v ); } );
        if ( !empty( $_items ) ) {
            $_dest_line = array_shift( $_items );
            $_parses = explode( ' ', $_dest_line );
            $dest_file_path = array_pop( $_parses );
            $dest_file_name = pathinfo( $dest_file_path, PATHINFO_FILENAME );
        } else {
            $_items = array_filter( $verbose, function( $_v ) { return preg_match( '/\shas\salready\sbeen\sdownloaded/', $_v ); } );
            if ( !empty( $_items ) ) {
                $_dest_line = array_shift( $_items );
                $_parses = explode( ' ', str_replace( '[download] ', '', $_dest_line ) );
                $dest_file_path = array_shift( $_parses );
                $dest_file_name = pathinfo( $dest_file_path, PATHINFO_FILENAME );
            }
        }
    }
    $verbose_log = str_replace( [ "\r\n", "\r", "\n" ], "\n", implode( PHP_EOL, $verbose ) );
    logger( __FUNCTION__, $cmd, $result, $verbose_log, $dest_file_path, $dest_file_name );
    return [
        'status'      => $result == 0,
        'response'    => $verbose,
        'destination' => [
            'path' => $dest_file_path,
            'name' => $dest_file_name,// = id
        ],
    ];
}

function edit( array $options ): array {
    global $opts;
    if ( !is_array( $options ) ) {
        $option_str = (string)$options;
    } elseif ( is_vector( $options ) ) {
        $option_str = implode( ' ', $options );
    } else {
        $dest_path = $options['dest'];
        unset( $options['dest'] );
        array_walk( $options, function( string $_v, string $_k ) use( &$option_str ) { $option_str .= $_k .' '. $_v .' '; } );
        $option_str .= ' ' . $dest_path;
    }
    $cmd = sprintf( '%s %s', $opts['editor'], $option_str );
    exec( $cmd, $verbose, $result );
    if ( $result == 0 ) {
        @unlink( trim( $options['-i'], '"' ) );
    }
    logger( __FUNCTION__, $cmd, $result, implode( PHP_EOL, $verbose ) );
    return [
        'status'   => $result == 0,
        'response' => $verbose,
    ];
}

function __( string $text ): string {
    global $opts;
    if ( !empty( $opts['translations'] ) && isset( $opts['translations'][$text] ) && !empty( $opts['translations'][$text] ) ) {
        return $opts['translations'][$text];
    } else {
        //logger( $text );
        return $text;
    }
}

function logger( ...$args ): void {
    global $opts;
    if ( $opts['debug'] ) {
        error_log( json_encode( $args, JSON_PRETTY_PRINT ), 3, './debug.log' );
    }
}
