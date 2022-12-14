<!DOCTYPE html>
<html lang="<?= $opts['html']['lang'] ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= __( $opts['html']['title'] ) ?></title>
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  <link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.4/dist/flowbite.min.css" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
  <script src="https://unpkg.com/flowbite@1.5.4/dist/flowbite.js"></script>
  <style type="text/tailwidcss">
    @layer utilities {
      .content-auto {
        content-visibility: auto;
      }
    }
  </style>
</head>
<body>
<div class="p-4 sm-p-0 lg:content-auto">
<?php
  if ( isset( $res['message'] ) && !empty( $res['message'] ) ):
    $info_color = $res['status'] ? 'green' : 'red';
?>
  <div id="alert" class="flex p-4 mb-4 bg-<?= $info_color ?>-100 rounded-lg dark:bg-<?= $info_color ?>-200" role="alert">
    <svg aria-hidden="true" class="flex-shrink-0 w-5 h-5 text-<?= $info_color ?>-700 dark:text-<?= $info_color ?>-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
    <span class="sr-only"><?= __( $res['status'] ? 'Info' : 'Error' ) ?></span>
    <div class="ml-3 text-sm font-medium text-<?= $info_color ?>-700 dark:text-<?= $info_color ?>-800">
      <?= __( $res['message'] ) ?>
    </div>
    <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-<?= $info_color ?>-100 text-<?= $info_color ?>-500 rounded-lg focus:ring-2 focus:ring-<?= $info_color ?>-400 p-1.5 hover:bg-<?= $info_color ?>-200 inline-flex h-8 w-8 dark:bg-<?= $info_color ?>-200 dark:text-<?= $info_color ?>-600 dark:hover:bg-<?= $info_color ?>-300" data-dismiss-target="#alert" aria-label="Close">
      <span class="sr-only"><?= __( 'Close' ) ?></span>
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
    </button>
  </div>
<?php endif; ?>
  <div class="md:grid md:grid-cols-3 md:gap-6">
    <div class="md:col-span-1">
      <div class="px-4 sm-px-0">
        <h1 class="text-lg font-medium leading-6 text-gray-900"><?= __( $opts['html']['title'] ) ?></h1>
        <p class="mt-1 text-sm text-gray-600"><?= __( 'Extracts and downloads video and audio from the specified video URL.' ) ?></p>
      </div>
<?php if ( isset( $details['info'] ) ): ?>
      <div class="mt-4 px-4 sm-px-0">
        <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
          <figure class="max-w-lg">
            <img class="max-w-full h-auto rounded-t-lg" src="<?= $details['info']['thumbnail'] ?>" alt="<?= $details['info']['title'] ?>">
          </figure>
          <div class="p-5">
            <h5 class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white"><?= $details['info']['title'] ?></h5>
            <p class="mb-3 text-sm font-normal text-gray-700 dark:text-gray-400"><?= $details['info']['description'] ?></p>
          </div>
        </div>
      </div>
<?php endif; ?>
    </div>
    <div class="mt-5 md:col-span-2 md:mt-0">
      <form method="post" action="#">
        <input type="hidden" name="action" value="<?= $action === 'playlist' ? 'get_details' : $action ?>">
<?php if ( isset( $details['info'] ) && isset( $details['info']['title'] ) ): ?>
        <input type="hidden" name="title" value="<?= $details['info']['title'] ?>">
<?php endif; ?>
        <div class="shadow sm:overflow-hidden sm:rounded-md">
          <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
            <div class="grid grid-cols-3 gap-6">
              <div class="col-span-3 gap-6">
                <label
                  for="target-url"
                  class="block text-sm font-medium text-gray-700"
                ><?= __( 'Target URL' ) ?></label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">https://</span>
                  <input
                    type="text"
                    name="url"
                    id="target-url"
                    value="<?= !empty( $target_url ) ? $target_url : '' ?>"
                    placeholder="<?= __( 'URL of video page, etc.' ) ?>"
                    <?php if ( $action !== 'get_details' ): ?>readonly<?php endif; ?>
                    class="block w-full flex-1 rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm<?php if ( $action !== 'get_details' ): ?> text-gray-400 dark:text-gray-500<?php endif; ?>"
                  >
                </div>
                <p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400"><?= __( 'Enter the URL of the video page you wish to download.' ) ?></p>
              </div>
            </div>
            <div class="grid grid-cols-3 gap-6">
              <div class="col-span-3 gap-6">
                <label
                  class="block text-sm font-medium text-gray-700"
                ><?= __( 'URL Analyzing Option:' ) ?></label>
                <div class="mt-1 flex">
                  <div class="flex items-center mr-4">
                    <input
                      id="inline-radio"
                      type="radio"
                      value="single"
                      name="analyze_type"
                      <?php if ( $action !== 'playlist' ): ?>checked<?php endif; ?>
                      <?php if ( $action !== 'get_details' ): ?>disabled<?php endif; ?>
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <label
                      for="inline-radio"
                      class="ml-2 text-sm font-normal <?php if ( $action !== 'get_details' ): ?>text-gray-400 dark:text-gray-500<?php else: ?>text-gray-900 dark:text-gray-300<?php endif; ?>"
                    ><?= __( 'As a single media file' ) ?></label>
                  </div>
                  <div class="flex items-center mr-4">
                    <input
                      id="inline-2-radio"
                      type="radio"
                      value="playlist"
                      name="analyze_type"
                      <?php if ( $action === 'playlist' ): ?>checked<?php endif; ?>
                      <?php if ( $action !== 'get_details' ): ?>disabled<?php endif; ?>
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <label
                      for="inline-2-radio"
                      class="ml-2 text-sm font-normal <?php if ( $action !== 'get_details' ): ?>text-gray-400 dark:text-gray-500<?php else: ?>text-gray-900 dark:text-gray-300<?php endif; ?>"
                    ><?= __( 'As the play-list media' ) ?></label>
                  </div>
                </div>
              </div>
            </div>

<?php if ( isset( $details['playlist'] ) && !empty( $details['playlist'] ) ): ?>
            <div>
              <label
                for="playlist"
                class="block text-sm font-medium text-gray-700"
              ><?= __( 'Playlist' ) ?></label>
              <ul class="w-full mb-2 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
<?php foreach ( $details['playlist'] as $_idx => $_item ): ?>
                <li class="w-full rounded-t-lg border-b border-gray-200 dark:border-gray-600">
                  <div class="flex items-center pl-3">
                    <input
                      id="list-radio-item-<?= $_idx + 1 ?>"
                      type="radio"
                      value="<?= $_item['url'] ?>"
                      name="playlist_item"
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                    >
                    <label
                      for="list-radio-item-<?= $_idx + 1 ?>"
                      class="py-3 ml-2 w-full text-sm font-medium text-gray-900 dark:text-gray-300"
                    ><?= $_item['title'] ?></label>
                    <p
                      id="helper-radio-text-<?= $_idx + 1 ?>"
                      class="w-40 text-xs font-normal text-gray-500 dark:text-gray-300"
                    >&mdash; <?= $_item['id'] ?></p>
                  </div>
                </li>
<?php endforeach; ?>
              </ul>
            </div>
<?php endif; ?>

<?php if ( isset( $details['format'] ) && !empty( $details['format'] ) ): ?>
            <div>
              <label
                for="details"
                class="block text-sm font-medium text-gray-700"
              ><?= __( 'Details' ) ?></label>
              <input type="hidden" id="choose-extension" name="extension" value="">
              <ul class="w-full mb-2 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
<?php foreach ( $details['format'] as $_idx => $_item ): ?>
                <li class="w-full rounded-t-lg border-b border-gray-200 dark:border-gray-600">
                  <div class="flex items-center pl-3">
                    <input
                      id="list-radio-item-<?= $_idx + 1 ?>"
                      type="radio"
                      value="<?= $_item['format_code'] ?>"
                      data-extension="<?= $_item['extension'] ?>"
                      name="format_code"
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                    >
                    <label
                      for="list-radio-item-<?= $_idx + 1 ?>"
                      class="py-3 ml-2 w-28 text-sm font-medium text-gray-900 dark:text-gray-300"
                    ><?= $_item['format_code'] ?> &mdash; <?= $_item['extension'] ?></label>
                    <p
                      id="helper-radio-text-<?= $_idx + 1 ?>"
                      class="w-full text-xs font-normal text-gray-500 dark:text-gray-300"
                    ><?= $_item['resolution_note'] ?></p>
                  </div>
                </li>
<?php endforeach; ?>
              </ul>
              <div class="col-span-3 gap-6 flex">
                <div class="mt-2">
                  <label
                    for="output-format"
                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-white"
                  ><?= __( 'Output Format' ) ?></label>
                  <select 
                    id="output-format"
                    name="output_format"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  >
                    <option value="" selected><?= __( 'Keep in original format' ) ?></option>
                    <option value="mp4"><?=  __( 'MP4 Video format' )  ?></option>
                    <option value="mp3"><?=  __( 'MP3 Audio format' )  ?></option>
                    <option value="m4a"><?=  __( 'M4a Audio format' )  ?></option>
                    <option value="wabm"><?= __( 'WEBM Video format' ) ?></option>
                    <option value="wav"><?=  __( 'Wave Audio format' ) ?></option>
                  </select>
                  <p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400"><?= __( 'Optional convert downloaded media.' ) ?></p>
                </div>
                <div class="mt-2">
                  <label
                    for="audio-quality"
                    class="block mb-3 text-sm font-medium text-gray-700 dark:text-white"
                  ><?= __( 'Audio Quality:' ) ?> 
                    <span
                      id="audio-quality-level"
                      class="mx-1 text-sm font-normal text-gray-500 dark:text-gray-400"
                    >9</span>
                  </label>
                  <input
                    id="audio-quality"
                    type="range"
                    name="audio_quality"
                    min="0"
                    max="9"
                    value="9"
                    disabled
                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                  >
                  <p id="helper-text-explanation" class="mt-4 text-sm text-gray-500 dark:text-gray-400"><?= __( 'Converted audio quality level: 9 = highest, 0 = lowest.' ) ?></p>
                </div>
              </div>
            </div>
<?php endif; ?>

            <div class="px-4 py-2 text-right sm:px-6 flex justify-end items-center">
              <button
                id="btn-exec"
                type="submit"
                disabled
                class="w-36 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 text-center inline-flex justify-center items-center"
              ><?= __( $action === 'download' ? 'Download' : 'Get Details' ) ?></button>
              <button
                id="btn-proc"
                disabled
                type="button"
                class="w-36 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 text-center inline-flex justify-center items-center hidden"
              >
                <svg aria-hidden="true" role="status" class="inline mr-3 w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                  <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                </svg>
                <?= __( $action === 'download' ? 'Processing...' : 'Getting...' ) ?>
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="bg-white px-4 py-3 text-center sm:px-6">
    <div class="inline-flex justify-center items-center w-full">
      <hr class="my-8 w-4/5 h-px bg-gray-200 border-0 dark:bg-gray-700">
      <span class="absolute left-1/2 px-3 font-medium text-gray-900 bg-white -translate-x-1/2 dark:text-white dark:bg-gray-900"><?= __( 'or' ) ?></span>
    </div>
    <button
      id="btn-refs"
      type="button"
      class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
    ><?= __( 'Refresh' ) ?></button>
  </div>
</div>
<footer class="relative inset-x-0 bottom-0 h-8">
<?php if ( isset( $opts['downloader_name'] ) || isset( $opts['downloader_name'] ) ) : ?>
  <div class="text-xs font-light text-center text-gray-900 bg-white">
    <label><?= __( 'Powered with' ) ?></label>
<?php   if ( isset( $opts['downloader_name'] ) ) : ?>
    <span><?= $opts['downloader_name'] ?> <?= $opts['downloader_version'] ?>;</span>
<?php   endif;
        if ( isset( $opts['editor_name'] ) ) : ?>
    <span><?= $opts['editor_version'] ?></span>
<?php   endif; ?>
  </div>
<?php endif; ?>
</footer>
<div id="popup-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
  <div class="relative w-full h-full max-w-md md:h-auto">
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
      <div class="p-6 text-center">
        <div role="status">
          <svg class="inline mr-2 w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
          </svg>
          <span class="sr-only"><?= __( 'Processing...' ) ?></span>
        </div>
        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"><?= __( 'Please wait a while...' ) ?></h3>
      </div>
    </div>
  </div>
</div>
<script>
const inputURL  = document.getElementById('target-url');
const chooseExt = document.getElementById('choose-extension');
const listItems = document.querySelectorAll('[id^="list-radio-item-"]');
const outFormat = document.getElementById('output-format');
const auQuality = document.getElementById('audio-quality');
const btnExec = document.getElementById('btn-exec');
const btnProc = document.getElementById('btn-proc');
const btnRefs = document.getElementById('btn-refs');
const modalEl = document.getElementById('popup-modal');
const options = {
  placement: 'center-center',
  backdrop: 'static',
  backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
  onHide: () => {
    //console.log('modal is hidden')
  },
  onShow: () => {
    //console.log('modal is shown')
  },
  onToggle: () => {
    //console.log('modal has been toggled')
  }
};
const modal = new Modal(modalEl, options);

if (listItems.length > 0) {
  listItems.forEach(elm => {
    elm.addEventListener('change', evt => {
      if (evt.target.checked) {
        if ('format_code' === evt.target.name) {
          chooseExt.value = evt.target.dataset.extension;
        } else {
          inputURL.value = inputURL.value.replace(/\?.*$/, `?v=${evt.target.value}`)
          console.log(evt.target.value)
        }
        btnExec.removeAttribute('disabled');
      }
    }, false);
  })
<?php if ( $action === 'download' ): ?>
  outFormat.addEventListener('change', evt => {
    const _val = evt.target.value;
    if (['m4a', 'mp3', 'wav'].includes(_val)) {
      auQuality.removeAttribute('disabled');
    } else {
      auQuality.setAttribute('disabled', true);
    }
  }, false);
  auQuality.addEventListener('change', evt => {
    const level = evt.target.value;
    document.getElementById('audio-quality-level').textContent = level;
  }, false);
<?php endif; ?>
}

inputURL.addEventListener('change', evt => {
  const _val = evt.target.value;
  if (_val) {
    if (/^https?:\/\/.*$/.test(_val)) {
      evt.target.value = _val.replace(/^https?:\/\/(.*)$/, '$1');
    }
    btnExec.removeAttribute('disabled');
  } else {
    btnExec.setAttribute('disabled', true);
  }
}, false);

btnRefs.addEventListener('click', evt => {
  evt.target.setAttribute('disabled', true);
  window.location.replace('/?action=initialize');
}, false);

btnExec.addEventListener('click', evt => {
<?php if ( $action === 'download' ): ?>
  btnRefs.setAttribute('disabled', true);
<?php endif; ?>
  evt.target.classList.add('hidden');
  btnProc.classList.remove('hidden');
  modal.show();
}, false);
</script>
</body>
</html>