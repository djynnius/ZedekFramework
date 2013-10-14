The themes are located in the /zedek/themes folder and contain 4 files:
#header.html
#footer.html
#style.css
#script.js

The theme may be selected by setting within the config file /zedek/config/config.json

    {
        "version": "2.0", 
        "theme": "new_theme"
    }

this would then require that a new theme folder with the name new_theme is created within the themes folder.

Templating applies to the theme, thus allowing for placeholders within the header and footer files.