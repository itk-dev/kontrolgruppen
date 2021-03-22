@Library('jenkins-pipeline')_

pipeline {
    agent any
    stages {
        stage ('Install') {
            when {
                expression { BRANCH_NAME ==~ /(release|master)/ }
            }
            parallel {
                stage('Composer') {
                    steps {
                        sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw --env COMPOSER_VERSION=1 itkdev/php7.4-fpm:latest composer install'
                    }
                }
                stage('Yarn') {
                    steps {
                        sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.yarn-cache:/usr/local/share/.cache/yarn:rw itkdev/yarn:latest install'
                    }
                }
            }
        }
        stage('Build encore assets') {
            when {
                expression { BRANCH_NAME ==~ /(release|master)/ }
            }
            steps {
                // Build encore assets
                sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.yarn-cache:/usr/local/share/.cache/yarn:rw itkdev/yarn:latest build'
            }
        }
        stage('Deployment staging') {
            when {
                branch 'release'
            }
            steps {
                // Update git repos.
                sh "ansible admwebitk01 -m shell -a 'cd /data/www/stg_kontrolgruppen_itkdev_dk/htdocs; git clean -d --force'"
                sh "ansible admwebitk01 -m shell -a 'cd /data/www/stg_kontrolgruppen_itkdev_dk/htdocs; git checkout ${BRANCH_NAME}'"
                sh "ansible admwebitk01 -m shell -a 'cd /data/www/stg_kontrolgruppen_itkdev_dk/htdocs; git fetch'"
                sh "ansible admwebitk01 -m shell -a 'cd /data/www/stg_kontrolgruppen_itkdev_dk/htdocs; git reset origin/${BRANCH_NAME} --hard'"

                // Run composer.
                sh "ansible admwebitk01 -m shell -a 'cd /data/www/stg_kontrolgruppen_itkdev_dk/htdocs; APP_ENV=prod composer install --no-dev -o'"

                // Run migrations.
                sh "ansible admwebitk01 -m shell -a 'cd /data/www/stg_kontrolgruppen_itkdev_dk/htdocs; sed -i \"s/ENCRYPTED = YES//\" ./src/Migrations/Version*; APP_ENV=prod php bin/console doctrine:migrations:migrate --no-interaction'"

                // Copy encore assets.
                sh "ansible admwebitk01 -m synchronize -a 'src=${WORKSPACE}/public/build/ dest=/data/www/stg_kontrolgruppen_itkdev_dk/htdocs/public/build'"

                // Clear cache
                sh "ansible admwebitk01 -m shell -a 'cd /data/www/stg_kontrolgruppen_itkdev_dk/htdocs; APP_ENV=prod php bin/console cache:clear'"
            }
        }
        stage('Deployment production') {
            when {
                branch 'master'
            }
            steps {
                timeout(time: 30, unit: 'MINUTES') {
                    input 'Should the site be deployed?'
                }
                // Update git repos.
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/tjek1_aarhuskommune_dk/htdocs; git clean -d --force'"
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/tjek1_aarhuskommune_dk/htdocs; git checkout ${BRANCH_NAME}'"
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/tjek1_aarhuskommune_dk/htdocs; git fetch'"
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/tjek1_aarhuskommune_dk/htdocs; git reset origin/${BRANCH_NAME} --hard'"

                // Run composer.
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/tjek1_aarhuskommune_dk/htdocs; APP_ENV=prod composer install --no-dev -o'"

                // Run migrations.
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/tjek1_aarhuskommune_dk/htdocs; APP_ENV=prod php bin/console doctrine:migrations:migrate --no-interaction'"

                // Copy encore assets.
                sh "ansible srvappkongruppe -m synchronize -a 'src=${WORKSPACE}/public/prod dest=/data/www/tjek1_aarhuskommune_dk/htdocs/public/'"

                // Clear cache
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/tjek1_aarhuskommune_dk/htdocs; APP_ENV=prod php bin/console cache:clear'"
            }
        }
    }
}
