// The require statement tells Node to look into the node_modules folder for a package
// Once the package is found, we assign its contents to the variable
// gulp.src tells the Gulp task what files to use for the task
// gulp.dest tells Gulp where to output the files once the task is completed.
var gulp = require('gulp'),
    browserSync = require('browser-sync').create(),
    sass = require('gulp-sass'),
    handlebars = require('gulp-compile-handlebars'),
    rename = require('gulp-rename'),
    popper = require('popper.js'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    del = require('del'),
    panini = require('panini'),
    uglify = require('gulp-uglify'),
    sourcemaps = require('gulp-sourcemaps'),
    imagemin = require('gulp-imagemin'),
    cache = require('gulp-cache'),
    runSequence = require('run-sequence'),
    cssnano = require('gulp-cssnano'),
    autoprefixer = require('gulp-autoprefixer');


// ------------ Development Tasks -------------
// Compile Sass into CSS
gulp.task('sass', function () {
    return gulp.src(['src/scss/*.scss'])
        .pipe(sourcemaps.init())
        .pipe(sass({ 
            outputStyle: 'expanded',
            sourceComments: 'map',
            sourceMap: 'sass',
            outputStyle: 'nested'
        }).on('error', sass.logError))
        .pipe(autoprefixer('last 2 versions'))
        //.pipe(cssnano()) // Use cssnano to minify CSS
        .pipe(sourcemaps.write())
        .pipe(gulp.dest("dist/css"))
        .pipe(browserSync.stream());
        console.log('Compiling scss');
});

// Using panini, template, page and partial files are combined to form html markup
gulp.task('compile-html', function () {
    return gulp.src('src/pages/**/*.html')
        .pipe(panini({
            root: 'src/pages/',
            layouts: 'src/layouts/',
            partials: 'src/partials/',
            helpers: 'src/helpers/',
            data: 'src/data/'
        }))
        .pipe(gulp.dest('dist'));
        console.log('Compiling partials with Panini');
});

// Reset Panini's cache of layouts and partials
gulp.task('resetPages', (done) => {
    panini.refresh();
    done();
    console.log('Clearing panini cache');
});

// Watches for changes while gulp is running
gulp.task('watch', ['sass'], function () {
    // Live reload with BrowserSync
    browserSync.init({
        server: "./dist"
    });
    gulp.watch(['./src/{data}/**/*'], [panini.refresh]);
    gulp.watch(['src/js/partials/*.js'], ['scripts', browserSync.reload]);
    gulp.watch(['node_modules/bootstrap/scss/bootstrap.scss', 'src/scss/**/*'], ['sass', browserSync.reload]);
    gulp.watch(['src/images/**/*'], ['images']);
    gulp.watch(['src/vid/**/*'], ['media']);
    gulp.watch(['src/**/*.html'], ['resetPages', 'compile-html', browserSync.reload]);
    console.log('Watching for changes');
});


// ------------ Optimization Tasks -------------






// Copies image files to dist
gulp.task('images', function () {
    return gulp.src('src/images/**/*.+(png|jpg|jpeg|gif|svg)')
        .pipe(cache(imagemin ())) // Caching images that ran through imagemin
        .pipe(gulp.dest('dist/images/'));
        console.log('Optimizing images');
});

// Copies video assets to dist
gulp.task('media', function () {
    return gulp.src('src/media/**/*')
        .pipe(gulp.dest('dist//media/'));
        console.log('Copying media into dist folder');
});

// Places font files in the dist folder
gulp.task('iconfonts', function () {
    return gulp.src([
        'node_modules/linearicons/dist/web-font/fonts/*.eot',
        'node_modules/linearicons/dist/web-font/fonts/*.svg',
        'node_modules/linearicons/dist/web-font/fonts/*.ttf',
        'node_modules/linearicons/dist/web-font/fonts/*.woff',
        'node_modules/linearicons/dist/web-font/fonts/*.woff2'

    ])
.pipe(gulp.dest("dist/fonts"))
.pipe(gulp.dest("src/fonts"))
        .pipe(browserSync.stream());
    console.log('Copying  icon fonts into dist folder');
});


// Places font files in the dist folder
gulp.task('owl', function () {
    return gulp.src([
        'node_modules/owl.carousel/dist/*.js',
    ])
        .pipe(gulp.dest("src/js/vendors/"))
        .pipe(browserSync.stream());
    console.log('Copying  owl');
});


// Places font files in the dist folder
gulp.task('font', function () {
    return gulp.src([
            'src/fonts/*.eot', 
            'src/fonts/*.woff', 
            'src/fonts/*.ttf', 
            'src/fonts/*.otf',
            'src/fonts/*',

        ])
        .pipe(gulp.dest("dist/fonts"))
        .pipe(browserSync.stream());
        console.log('Copying fonts into dist folder');
});


// Places font files in the dist folder
gulp.task('fontAwesome', function () {
    return gulp.src([

        'src/fonts/font-awesome/*',
        'src/fonts/font-awesome/*.eot',
        'src/fonts/font-awesome/*.woff',
        'src/fonts/font-awesome/*.ttf',
        'src/fonts/font-awesome/*.otf',

    ])
        .pipe(gulp.dest("dist/fonts/font-awesome"))
        .pipe(browserSync.stream());
    console.log('Copying fonts into dist folder');
});



// Concatenating js files
gulp.task('scripts', function () {
    // jQuery first, then Popper.js, then Bootstrap JS, then other JS libraries, and last app-imp-new.js
    return gulp.src([
            'src/js/vendors/jquery.min.js', 
            'src/js/vendors/popper.min.js', 
            'src/js/vendors/bootstrap.min.js',
            'src/js/vendors/owl.carousel.js',
            'src/js/app-imp-new.js'
        ])
        .pipe(sourcemaps.init())
        .pipe(concat('app-imp-new.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('dist/js/'))
        .pipe(browserSync.stream());
        console.log('Concatenating JavaScript files into single file');
});

// Cleaning/deleting files no longer being used in dist folder
gulp.task('clean:dist', function () {
    console.log('Removing old files from dist');
    return del.sync('dist');
});


// ------------ Build Sequence -------------
// Simply run 'gulp' in terminal to run local server and watch for changes
gulp.task('default', ['clean:dist', 'iconfonts', 'font', 'owl', 'fontAwesome',  'scripts', 'images', 'compile-html', 'resetPages', 'media', 'watch']);

// Creates production ready assets in dist folder
gulp.task('build', function () {
    console.log('Building production ready assets');
    runSequence('clean:dist', 'sass', ['scripts', 'images', 'iconfonts', 'font', 'fontAwesome', 'compile-html'])
});
