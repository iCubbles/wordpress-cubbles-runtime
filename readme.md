# cubbles-runtime

This is a small Wordpress plugin which adds Cubbles support to your wordpress instance.

[Want to get to know the Cubbles Platform?](https://cubbles.github.io)

# Usage
Just copy the folder `cubbles-runtime` into your `wp-content/plugins` folder and activate plugin using admin panel. If you create a new blog post or page just switch to raw html editor and paste

    <travel-planner cubx-dependency="com.incowia.demo.travel-planner@0.1.0-SNAPSHOT/travel-planner/main"></travel-planner>

This includes the `travel-planner` demo into your blog post.

## Options
All users who have the capability `manage_options` are allowed to change the configuration of the plugin. You can find plugin settings as ab submenu of the standard wordress settings. Look for `Cubbles` inside the submenu.
Currently there are three settings available:

1. **Remote Store Url**: Add the url of the store from which you would like the get the Cubbles Runtime and the cubbles coponents itself. *(e.g. `https://cubbles.world/sandbox`)*
2. **CRE Webpackage**: Define wich version of CRE (**C**lient **R**untime **E**xtension) you would like to use. You have to specify the complete Webpackage name. *Note:* This has to be at least version `1.9.0-SNAPSHOT` *(e.g. `cubx.core.rte@1.9.0`)*
3. **Allowed Cubbles Components**: Configure a list of cubbles components which users can use even if they don't have the capability `unfiltered_html`.

## Known Issues

1. Wordpress Editor: CustomTags are shown in `[TextView]` only.
2. Wordpress Editor: An empty line before a CustomTag lets the editor remove the CustomTag completely when switching between `[VisualView]` and `[TextView]`  