const fs = require('fs');
const { src, dest } = require('gulp');
const uglify = require('gulp-uglify-es').default;
const rename = require('gulp-rename');
const chokidar = require('chokidar');
const yaml = require('js-yaml');
const run = require('gulp-run');
const plumber = require('gulp-plumber');
const eslint = require('gulp-eslint');
const gulpIf = require('gulp-if');

require('dotenv').config({path: '.env.local'});

const isSilentMode = process.argv[3] === '--silent';
const isWatcherTask = process.argv[2] === 'watch';

if (isWatcherTask && process.env.APP_ENV !== 'dev') {
  console.warn(
    '\x1b[31m%s\x1b[0m',
    'You should set APP_ENV variable to "dev" parameter in your .env.local file'
  );
  process.exit(0);
}

const skinModelPath = 'config/dynamic/xcart_skin_model.yaml';
let skinModel;

try {
  skinModel = yaml.load(fs.readFileSync(skinModelPath, 'utf8'));
} catch (error) {
  console.warn(
    '\x1b[31m%s\x1b[0m',
    `There was an error during reading "config/dynamic/xcart_skin_model.yaml" file: ${error}`
  );
  process.exit(0);
}

const isModulePath = (path) => /^modules/.test(path);

const getActualFile = (path, remove, entries, extension) => {
  const folders = path.split('/');
  const isModule = isModulePath(path);
  // type, e.g. web, mail, pdf
  const type = isModule ? folders[4] : folders[1];
  // zone, e.g. common, admin, customer
  const zone = isModule ? folders[5] : folders[2];
  let replaceDir = isModule ? 'public' : 'assets';
  const shortPath = isModule
    ? path.split(`/public/${type}/${zone}/`).pop()
    : path.split(`assets/${type}/${zone}/`).pop();

  let result;
  let paths = skinModel.parameters['xcart.skin_model'][type][zone];

  if (remove) {
    paths = entries;

    if (fs.existsSync(`public/assets/${type}/${zone}/${shortPath}`)) {
      fs.rmSync(`public/assets/${type}/${zone}/${shortPath}`);
    }

    if (extension === 'js' && shortPath.indexOf('.min.js') < 0) {
      const minified = shortPath.replace('.js', '.min.js');

      if (fs.existsSync(`public/assets/${type}/${zone}/${minified}`)) {
        fs.rmSync(`public/assets/${type}/${zone}/${minified}`);
      }
    }
  }

  if (!paths) {
    return null;
  }

  for (let idx = 0; idx < paths.length; idx++) {
    if (remove) {
      replaceDir = isModulePath(paths[idx]) ? 'public' : 'assets';
    }

    if (fs.existsSync(`${paths[idx].replace(/{{type}}/, replaceDir)}/${shortPath}`)) {
      result = `${paths[idx].replace(/{{type}}/, replaceDir)}/${shortPath}`;
      break;
    }
  }

  return result;
}

const processFile = (file, path, fileExtention, zone) => {
  let replaceAdminDir = path.replace(/common/,'admin');
  let replaceCustomerDir = path.replace(/common/,'customer');

  if (zone === 'common') {
    if (fs.existsSync(file.replace(/common/,'admin'))) {
      replaceAdminDir = path;
    }

    if (fs.existsSync(file.replace(/common/,'customer'))) {
      replaceCustomerDir = path;
    }
  }

  if (fileExtention === 'js' && file.indexOf('.min.js') === -1) {
    if (zone === 'common') {
      return src(file, { allowEmpty: true })
        .pipe(plumber())
        .pipe(gulpIf(isWatcherTask, eslint()))
        .pipe(gulpIf(isWatcherTask, eslint.failOnError()))
        .pipe(dest(`public/${path}`))
        .pipe(dest(`public/${replaceAdminDir}`))
        .pipe(dest(`public/${replaceCustomerDir}`))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(dest(`public/${path}`))
        .pipe(dest(`public/${replaceAdminDir}`))
        .pipe(dest(`public/${replaceCustomerDir}`));
    }

    return src(file, { allowEmpty: true })
      .pipe(plumber())
      .pipe(gulpIf(isWatcherTask, eslint()))
      .pipe(gulpIf(isWatcherTask, eslint.failOnError()))
      .pipe(dest(`public/${path}`))
      .pipe(uglify())
      .pipe(rename({suffix: '.min'}))
      .pipe(dest(`public/${path}`));
  }

  if (zone === 'common') {
    return src(file, { allowEmpty: true })
      .pipe(plumber())
      .pipe(dest(`public/${path}`))
      .pipe(dest(`public/${replaceAdminDir}`))
      .pipe(dest(`public/${replaceCustomerDir}`));
  }

  return src(file, { allowEmpty: true })
    .pipe(plumber())
    .pipe(dest(`public/${path}`));
}

const build = (file, remove = false, entries = []) => {
  const fileName = file.replace(/^.*[\\/]/, '');
  const extension = fileName.substring(fileName.lastIndexOf('.') + 1);

  if (extension === 'less') {
    return fs.rmSync('public/var/resources', { recursive: true, force: true });
  } else if (extension === 'js') {
    fs.rmSync('public/var/resources/js', { recursive: true, force: true });
  }

  let actualFile = getActualFile(file, remove, entries, extension);

  if (!actualFile) {
    return false;
  }

  let filePath = actualFile.replace(fileName, '');
  let zone = actualFile.split('/')[2];

  if (isModulePath(actualFile)) {
    filePath = `assets/${filePath.split('/public/').pop()}`;
    zone = actualFile.split('/')[5];
  }

  return processFile(actualFile, filePath, extension, zone);
}

const minifyJS = (path) => {
  if (path.indexOf('.min.js') > -1) {
    return false;
  }

  fs.rmSync('public/var/resources/js', { recursive: true, force: true });

  return src(path, { allowEmpty: true })
    .pipe(uglify())
    .pipe(rename({suffix: '.min'}))
    .pipe(dest(function (path) {
      return path.base;
    }));
}

const refreshCacheTimestamp = () => {
  const refreshCacheTimestampCmd = new run.Command(
    'php bin/console xcart:ll:refresh-cache-timestamp',
    { verbosity: isSilentMode ? 0 : 2 }
  );

  return new Promise((resolve) => {
    refreshCacheTimestampCmd.exec(null, resolve)
  });
}

const entries = [
  'assets/**',
  'modules/**/public/**'
];

let skinModelEntries = [];

Object.keys(skinModel.parameters['xcart.skin_model']).forEach((type) => {
  Object.values(skinModel.parameters['xcart.skin_model'][type]).map((row) => {
    row = row.map((item) => {
      return isModulePath(item)
        ? item.replace(/{{type}}/, 'public')
        : item.replace(/{{type}}/, 'assets');
    });

    skinModelEntries = [...skinModelEntries, ...row];
  })
});

const watchAssets = () => {
  const watcher = chokidar.watch(entries, {
    persistent: true,
    ignored: [/node_modules/, 'assets/*.*', '**/.*'],
    ignoreInitial: true
  });

  watcher.on('change', (path) => {
    refreshCacheTimestamp();

    console.info(
      '\x1b[36m%s\x1b[0m',
      `${path} is changed`
    );

    return build(path);
  }).on('add', (path) => {
    console.info(
      '\x1b[36m%s\x1b[0m',
      `${path} is added`
    );

    return build(path);
  }).on('unlink', (path) => {
    console.info(
      '\x1b[36m%s\x1b[0m',
      `${path} is removed`
    );


    return build(path, true, skinModelEntries);
  });
}

const installAssets = () => {
  const watcher = chokidar.watch(entries, {
    persistent: true,
    ignored: [/node_modules/, 'assets/*.*', '**/.*']
  });

  if (!isSilentMode) {
    console.info(
      '\x1b[36m%s\x1b[0m',
      'Installing assets. Please wait'
    );
  }

  return new Promise((resolve) => {
    watcher
      .on('add', (path) => build(path))
      .on('ready', () => {
        watcher.close()
          .then(refreshCacheTimestamp)
          .then(() => {
            console.info(
              '\x1b[36m%s\x1b[0m',
              `Assets have been installed successfully`
            );

            resolve();
          });
      });
  })
}

const minifyAssets = () => {
  const watcher = chokidar.watch('public/assets/**/*.js', {
    persistent: true,
    ignored: [/node_modules/, 'public/assets/*.*', '**/.*']
  });

  if (!isSilentMode) {
    console.info(
      '\x1b[36m%s\x1b[0m',
      'Minifying js assets. Please wait'
    );
  }
  return new Promise((resolve) => {
    watcher
      .on('add', (path) => minifyJS(path))
      .on('ready', () => {
        watcher
          .close()
          .then(() => {
            console.info(
              '\x1b[36m%s\x1b[0m',
              `Assets have been minified successfully`
            );
            resolve();
          })
      });
  });
}

exports.install = installAssets;
exports.watch = watchAssets;
exports.minify = minifyAssets;
