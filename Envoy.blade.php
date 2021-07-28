@setup
    $server = 'root@94.130.173.184';
    $pathOnServer = "/home/projects/crafty";

    $repository = 'git@github.com:xdvx/crafty.git';
    $branch = 'master';
@endsetup

@servers(['production' => $server])

@task('app:start', ['on' => 'production'])
    cd {{ $pathOnServer }}
    echo "Bringing the application down"
    php artisan down || :

    php artisan optimize:clear
    php artisan clear-compiled
@endtask

@task('app:deploy.git.init', ['on' => 'production'])
    cd {{ $pathOnServer }}

    git init
    git remote add origin {{ $repository }}
@endtask

@task('app:deploy.git', ['on' => 'production'])
    cd {{ $pathOnServer }}

    echo "Pulling changes"
    git config core.fileMode false
    git reset --hard
    git pull origin {{ $branch }}
@endtask

@task('app:deploy.assets', ['on' => 'production'])
    cd {{ $pathOnServer }}

    echo "Updating composer packages"
    composer install --optimize-autoloader --no-dev

    echo "Updating npm packages"
    npm install

    echo "Building assets"
    npm run production
@endtask

@task('app:permissions', ['on' => 'production'])
    chown www-data:www-data -R {{ $pathOnServer }}
    chmod 0755 -R {{ $pathOnServer }}
@endtask

@task('app:finish', ['on' => 'production'])
    cd {{ $pathOnServer }}

    php artisan migrate  --force
    php artisan cache:clear
    php artisan config:cache
    php artisan route:cache
    php artisan event:cache
    php artisan view:cache

    echo "Bringing the application up"
    php artisan up
@endtask

@story('deploy')
    app:start
    app:deploy.git
    app:deploy.assets
    app:permissions
    app:finish
@endstory

@story('deploy:quick')
    app:start
    app:deploy.git
    app:permissions
    app:finish
@endstory

@error
    echo "Failed: [{$task}]", PHP_EOL;
@enderror
