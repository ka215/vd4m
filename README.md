# VD4M

This is the web user interface with PHP as **V**ideo **D**ownloader **For** **M**yself.

![Screenshot Image](/../develop/screenshot.jpg?raw=true "Screenshot Image")

## Getting Start

1. Create a config file named ".config" in the application root. The configure JSON format as followed:
```json
{
    "downloader": "{name or fullpath of application for downloading}",
    "editor": "{name or fullpath of application for encoding/decoding}",
    "dist_base": "%current_dir%\\downloads",
    "lang_dir": "%current_dir%\\langs",
    "html": {
        "lang": "auto",
        "title": "Video Downloader for Myself"
    },
    "dl_with_conv": false,
    "debug": false
}
```

2. Create the destination directory saved downloaded file depend on you specified in ".config".
3. You will host an index.php in the application root by using XAMPP etc., then you access that by browser.

## Extended

If you prepare lang file (JSON format) named same code of browser's accept language, you can translate to any language in web user interface as you likes.

## License

MIT License
