## Finest Dental Implants Tabbed Landing Page

#### Built With: SASS,GULP & Zurb Panini

1.	` cd `  into the FD-Implants-Tabbed-Landing-Page and run ` npm install`  in your terminal - this command installs packages listed in the package.json file and any packages that it depends on. Packages are installed in the node_modules directory. 
a.	Please do not modifiy anything in the `node_modules directory`. Note: the node_modules directory is not kept in source control. 
2.	To start your project, make sure you are in the Pattern Library folder and run ` gulp `  to start the “default” gulp task and bring up the local BrowserSync server. This step will also create the dist folder for you, run the Gulp tasks specified in the gulpfile.js file, and watch for file changes. When changes are ready to be comiited, stop the default gulp task, and commit to source control. 
3.	Your finished static mini site will be created in a folder called dist, viewable at this URL:
http://localhost:3000 
4.	To create compressed, production-ready assets run `gulp build`. This will delete everything in the dist folder and recreate all of your complied files. Never make updates directly into the dist folder as these files get overridden each time. Note: The dist folder is not kept in source control. 


#### Deployment

```txt
git clone -b FD-Implants-Tabbed-Landing-Page --single-branch git@bitbucket.org:finestdental/marketing.git FD-Implants-Tabbed-Landing-Page
cd FD-Zach-Tall-Landing-Page
npm install
gulp
```

#### Production ready build command
```txt
gulp build
```

#### Finest Dental Implants Tabbed Style Landing Page
![image](https://finestdental.co.uk/images/screenshots/screen-fd-implants-tabbed.jpg)

## Additional Resources:
- [Node js: JavaScript runtime built on Chrome's V8 JavaScript engine](https://nodejs.org/en/)
- [Gulp:Automate and enhance your workflow](https://gulpjs.org/getting-started)
- [Sass: Syntactically Awesome Style Sheets](http://sass-lang.com/)
- [Bootstrap 4](https://getbootstrap.com/)
- [Handlebars](http://handlebarsjs.com/)
- [Panini](https://github.com/zurb/panini) 

## Author
* **Kyle De Sousa** - [Finest Dental](https://finestdental.co.uk)