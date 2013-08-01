var Namespace = new Class({
    
    Implements: Options,
        
    options: {
        root:       window, // You can set the base for your namespace.  Defaults to `window`
        delimiter:  "."     // Delimiter for namespacing
    },
    
    // Accepts the namespace path "my.namespace.path" & the class options for instantiation
    initialize: function(namespace, options) {
    	options = options || {};
        if (options.namespace) {
            this.setOptions(options.namespace);
        };
        
        // Parse options for strings where classes should exist
        options = this.parseOptions(options);
        
        // Return the instantiated class
        return this.getClass(namespace, options);
    },
    
    parseOptions: function(options) {
        // Replace `Extends: "myClass"` with `Extends: myClass` instantiation
        var params = ["Implements", "Extends", "Requires"];
        
        // Iterate through each type of dependency (i.e. "Extends")
        params.each(function(param) {
            var resources = $splat(options[param]);
            
            resources.each(function(resource, i) {
                // If the dependency isn't a class yet, try to load the class
                if ($type(resource) === "string") {
                    // Get existing class or load it via SJAX
                    var resource = this.load(resource);
                    
                    // If class finally exists, assign it to it's key (for Requires)
                    // or to the param itself (for Extends)
                    if ($type(resource) === "class") {
                        if ($type(options[param]) === "array") {
                            options[param][i] = resource;
                        } else {
                            options[param] = resource;
                        }
                    } else {
                        if (param !== "Requires") {
                            throw new Error(param + " class \"" + resource + "\" does not exist or could not be loaded.");
                        }
                    }
                }
            }, this);
        }, this);
        
        return options;
    },
    
    // Traverses down the namespace path and returns the (newly instantiated if not existing) class
    getClass: function(namespace, options) {
        var root = this.options.root;
        
        // Iterate through each section of the namespace
        namespace.split(this.options.delimiter).each(function(name, i, names) {
            // Up until the last leaf, create an object if undefined
            if (i < names.length - 1) {
                if (!root[name]) {
                    root[name] = {};
                }
            } else {
                // If the last leaf doesn't exist & we're looking to instantiate, instantiate the class
                if (!root[name] && options) {
                    return root[name] = new Class(options);
                }
            };
            
            root = root[name];
        });
        
        // Return the requested namespaced class
        return root;
    },
    
    load: function(namespace) {
        (new Request({
            url:    Namespace.getBasePath(namespace) + ".js",
            method: 'GET',
            async:  false,
            evalResponse:   true
        })).send();
        
        return this.getClass(namespace);
    }
    
});

Namespace.paths = {
    _base: "."
};

Namespace.setBasePath = function(namespace, path) {
    if (!path) {
        var path = namespace;
        var namespace = "_base";
    }
    
    Namespace.paths[namespace] = path;
};

Namespace.getBasePath = function(namespace) {
    // Start with the base path
    var path = Namespace.paths._base;
    
    // Iterate through each specified namespace path ("Moo.Core" => "js/Moo/Core/Source")
    for (var stub in Namespace.paths) {
        if (stub === namespace.substring(0, stub.length)) {
            path += "/" + Namespace.paths[stub];
            // Remove stub from namespace, as we've already pathed it
            namespace = namespace.substring(stub.length + 1);
            break;
        }
    }
    
    return path + "/" + namespace.replace(/\./g, "/");
};

Namespace.require = function(namespaces) {
    $splat(namespaces).each(function(namespace) {
        new Namespace(namespace, { Requires: namespace });
    });
};

// Initialize base path based on Namespace script & document URL
;(function() {
    // Get the last script loaded (should be this script)
    var script = $$('script').getLast();
    // Trim off the script name
    var jsUrl = script.src.substring(0, script.src.lastIndexOf("/"));
    // Trim off the page name as well
    var baseUrl = document.URL.substring(0, document.URL.lastIndexOf("/") + 1);
    // Subtract page path from script path to get script subfolder
    var path = jsUrl.replace(baseUrl, '');
    
    Namespace.setBasePath(path);
})();