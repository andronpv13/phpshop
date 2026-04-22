<?php
session_start();
error_reporting(0);
if (empty($_SESSION['idPHPSHOP']))
    exit('Неавторизованный запрос');

if (empty($_GET['return']))
    $_GET['return'] = 'icon_new';

if (empty($_GET['resizable']))
    $resizable = 'false';
else
    $resizable = 'true';

//  UTF-8 Default Charset Fix
if (stristr(ini_get("default_charset"), "utf") and function_exists('ini_set')) {
    ini_set("default_charset", "cp1251");
}

// UTF-8 Env Fix
if (ini_get("mbstring.func_overload") > 0 and function_exists('ini_set')) {
    ini_set("mbstring.internal_encoding", null);
}

// Локализация
$locale = str_replace(array('russian', 'russian_utf', 'ukrainian', 'belarusian', 'english', 'english_utf'), array('ru', 'ru', 'uk', 'ru', 'en', 'en'), $_SESSION['lang']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="windows-1251">
        <title>Найти файл</title>

        <script data-main="./main.default.js" src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js"></script>




        <!-- elFinder initialization (REQUIRED) -->
        <script>

            var FileBrowserDialogue = {
                init: function () {
                    // Here goes your code for setting your custom things onLoad.
                },
                mySubmit: function (URL) {
                    // pass selected file path to TinyMCE
                    parent.tinymce.activeEditor.windowManager.getParams().setUrl(URL);

                    // force the TinyMCE dialog to refresh and fill in the image dimensions
                    var t = parent.tinymce.activeEditor.windowManager.windows[0];
                    t.find('#src').fire('change');

                    // close popup window
                    parent.tinymce.activeEditor.windowManager.close();
                }
            }

            define('elFinderConfig', {
                // elFinder options (REQUIRED)
                // Documentation for client options:
                // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
                defaultOpts: {
                    getFileCallback: function (data) {
                        file = data.url;

                        // Tinymce
                        if (parent.tinymce && parent.tinymce.activeEditor.windowManager.getParams()) {
                            FileBrowserDialogue.mySubmit(file);
                        }

                        // Window
                        else if (window.opener) {
                            window.opener.window.$('[data-icon="<?php echo $_GET['return']; ?>"]').html(file);
                            window.opener.window.$('input[name="<?php echo $_GET['return']; ?>"],#<?php echo $_GET['return']; ?>').val(file).change();
                            window.opener.window.$('.img-thumbnail[data-thumbnail="<?php echo $_GET['return']; ?>"]').attr('src', file);
                            window.opener.window.$('[data-icon="<?php echo $_GET['return']; ?>"]').prev('.glyphicon').removeClass('hide');
                            self.close();
                        }
                        // Redactor Modal
                        else if (parent.window.RedactorModalOpen > 0) {
                            parent.window.$("#mymodal-textarea").val(file);
                            parent.window.$('#elfinderModal').modal('hide');
                        }
                        // Quill
                        else if (parent.quill<?php echo '_' . str_replace('-', '', $_GET['return']); ?>) {
                            var range = parent.quill<?php echo '_' . str_replace('-', '', $_GET['return']); ?>.getSelection();
                            parent.quill<?php echo '_' . str_replace('-', '', $_GET['return']); ?>.insertEmbed(range.index, 'image', file);
                            parent.window.$('#elfinderModal').modal('hide');
                        }

                        // Modal
                        else if (parent.window) {
                            parent.window.$('[data-icon="<?php echo $_GET['return']; ?>"]').html(file);
                            parent.window.$('input[name="<?php echo $_GET['return']; ?>"],#<?php echo $_GET['return']; ?>').val(file).change();
                            parent.window.$('.img-thumbnail[data-thumbnail="<?php echo $_GET['return']; ?>"]').attr('src', file);
                            parent.window.$('[data-icon="<?php echo $_GET['return']; ?>"]').prev('.glyphicon').removeClass('hide');
                            parent.window.$('#elfinderModal').modal('hide');
                        }


                    },
                    resizable: <?php echo $resizable; ?>,
                    height: 500,
                    url: 'php/connector.php?path=<?php echo $_GET['path']; ?>',
                    lang: '<?php echo $locale ?>',
                    uiOptions: {
                        // toolbar configuration
                        toolbar: [
                            ['back', 'forward'],
                            // ['reload'],
                            // ['home', 'up'],
                            ['mkdir', 'upload'],
                            ['open', 'download', 'getfile'],
                            ['info'],
                            ['quicklook'],
                            ['copy', 'cut', 'paste'],
                            ['rm'],
                            ['duplicate', 'edit', 'resize'],
                            ['extract', 'archive'],
                            ['search'],
                            ['view']
                        ],
                        // directories tree options
                        tree: {
                            // expand current root on init
                            openRootOnLoad: true,
                            // auto load current dir parents
                            syncTree: true
                        },
                        // navbar options
                        navbar: {
                            minWidth: 150,
                            maxWidth: 500
                        },
                        // current working directory options
                        cwd: {
                            // display parent directory in listing as ".."
                            oldSchool: false
                        }
                    },
                    contextmenu: {
                        // navbarfolder menu
                        navbar: ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],
                        // current directory menu
                        cwd: ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'info'],
                        // current directory file menu
                        files: [
                            'getfile', '|', 'open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
                            'rm', '|', 'resize', '|', 'archive', 'extract', '|', 'info'
                        ]
                    },

                    // bootCalback calls at before elFinder boot up 
                    bootCallback: function (fm, extraObj) {
                        /* any bind functions etc. */
                        fm.bind('init', function () {
                            // any your code
                        });
                        // for example set document.title dynamically.
                        var title = document.title;
                        fm.bind('open', function () {
                            var path = '',
                                    cwd = fm.cwd();
                            if (cwd) {
                                path = fm.path(cwd.hash) || null;
                            }
                            document.title = path ? path + ':' + title : title;
                        }).bind('destroy', function () {
                            document.title = title;
                        });
                    }
                },
                managers: {
                    // 'DOM Element ID': { /* elFinder options of this DOM Element */ }
                    'elfinder': {}
                }
            });

        </script>
    </head>
    <body>

        <!-- Element where elFinder will be created (REQUIRED) -->
        <div id="elfinder"></div>
    </body>
</html>
