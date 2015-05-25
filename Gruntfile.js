/** 
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

'use strict';

module.exports = function ( grunt ) {
  
  // Project Configuration
  grunt.initConfig({
      
      pkg : grunt.file.readJSON('package.json'),
      
      uglify : {
        
        //uglify lib_anahita js files
        anahita : {
            options : {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n',
                compress: true
            },
            
            files : {
                'src/media/lib_anahita/js/production/site.js' : 
                ['src/media/lib_anahita/js/production/site.uncompressed.js'],
                
                'src/media/com_actors/js/min/cover.min.js' : 
                ['src/media/com_actors/js/cover.js'],
                
                'src/media/com_search/js/min/search.min.js' : 
                ['src/media/com_search/js/search.js'],
                
                'src/media/com_composer/js/min/composer.min.js' : 
                ['src/media/com_composer/js/composer.js']
            }
        },
        
        //uglify com_photos js files
        photos : {
            
            options : {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n',
                compress: true
            },
            
            files : {
                
                'packages/Photos/src/media/com_photos/js/min/organizer.min.js' : 
                ['packages/Photos/src/media/com_photos/js/organizer.js'],
                
                'packages/Photos/src/media/com_photos/js/min/photoset.min.js' : 
                ['packages/Photos/src/media/com_photos/js/photoset.js'],
                
                'packages/Photos/src/media/com_photos/js/min/upload.min.js' : 
                ['packages/Photos/src/media/com_photos/js/upload.js'],
            }
        },
        
        //uglify com_invites js files
        invites : {
            
            options : {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n',
                compress: true
            },
            
            files : {
                
                'packages/Invites/src/media/com_invites/js/min/email.min.js' : 
                ['packages/Invites/src/media/com_invites/js/email.js'],
                
                'packages/Invites/src/media/com_invites/js/min/facebook.min.js' : 
                ['packages/Invites/src/media/com_invites/js/facebook.js']
            }
        },
        
        //uglify com_subscriptions js files
        subscriptions : {
            
            options : {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n',
                compress: true
            },
            
            files : {
                
                'packages/Subscriptions/src/media/com_subscriptions/js/min/coupon.min.js' : 
                ['packages/Subscriptions/src/media/com_subscriptions/js/coupon.js'],
                
                'packages/Subscriptions/src/media/com_subscriptions/js/min/setting.min.js' : 
                ['packages/Subscriptions/src/media/com_subscriptions/js/setting.js']
            }
        }
        
      }
      
  });
  
  grunt.loadNpmTasks('grunt-contrib-uglify');
  
  grunt.registerTask('default', ['uglify']);  
  
};
