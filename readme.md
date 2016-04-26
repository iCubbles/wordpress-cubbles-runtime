#cubbles-runtime

This is a small Wordpress plugin which adds Cubbles support to your wordpress instance. Basically it does 3 things right now:
1. Add webcomponents-lite.js from cubbles.world sandbox store
2. Add cubbles clien runtime extension (CRE) from cubbles.world sandbox store
3. Adjust allowedposttags to enable admin user to use custom tags in post html editor

#Usage
Just copy the folder `cubbles-runtime` into your `wp-content/plugins` folder and active plugin using admin panel. If you create a new blog post just switch to raw html editor and paste
    <div cubx-core-crc>
        <travel-planner cubx-dependency="https://cubbles.world/sandbox/com.incowia.demo.travel-planner@0.1.0-SNAPSHOT/travel-planner/main"></travel-planner>
    </div>
This includes the `travel-planner` demo into your blog post.

#Restrictions

Currently the plugin is still under development and does only support the CRE version 1.8.0-SNAPSHOT from https://cubbles.world/sandbox store.
Also only the custom tag `<travel-planner>` is supported (see https://github.com/iCubbles/demo.travel-planner).

[Want to get to know the Cubbles Platform?](https://cubbles.github.io)
