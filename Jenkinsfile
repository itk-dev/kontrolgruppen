≥@Library('jenkins-pipeline')_

pipeline {
    agent any
    stages {
        stage ('Install') {
            parallel {
                stage('Composer') {
                    steps {
                        sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.2-fpm:latest composer install'
                    }
                }
                stage('Yarn') {
                    steps {
                        sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.yarn-cache:/usr/local/share/.cache/yarn:rw itkdev/yarn:latest install'
                    }
                }
            }
        }
        stage('Build and test') {
          parallel {
              stage('PHP') {
                stages {
                    stage('PHP7 compatibility') {
                        steps {
                            sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.2-fpm:latest vendor/bin/phan --allow-polyfill-parser'
                        }
                    }
                    stage('Coding standards') {
                        steps {
                            sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.2-fpm:latest vendor/bin/phpcs --standard=phpcs.xml.dist'
                            sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.2-fpm:latest vendor/bin/php-cs-fixer --config=.php_cs.dist fix --dry-run --verbose'
                            sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.2-fpm:latest vendor/bin/twigcs lint templates'
                        }
                    }
                }
            }
            stage('Yarn - encore') {
                    stages {
                        stage('Coding standards') {
                            steps {
                                sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.yarn-cache:/usr/local/share/.cache/yarn:rw itkdev/yarn:latest check-coding-standards'
                            }
                        }
                        stage('Build') {
                            steps {
                                sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.yarn-cache:/usr/local/share/.cache/yarn:rw itkdev/yarn:latest build'
                            }
                        }
                    }
                }
            }
        }
        stage('Deployment develop') {
            when {
                branch 'develop'
            }
            steps {
                sh 'echo "DEPLOY"'
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
                sh "ansible admwebitk01 -m shell -a 'cd /data/www/stg_kontrolgruppen_itkdev_dk/htdocs; APP_ENV=prod php bin/console doctrine:migrations:migrate --no-interaction'"

                // Copy encore assets.
                sh "ansible admwebitk01 -m synchronize -a 'src=${WORKSPACE}/public/build/ dest=/data/www/stg_kontrolgruppen_itkdev_dk/htdocs/public/build'"
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
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/kontrolgruppen_itkdev_dk/htdocs; git clean -d --force'"
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/kontrolgruppen_itkdev_dk/htdocs; git checkout ${BRANCH_NAME}'"
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/kontrolgruppen_itkdev_dk/htdocs; git fetch'"
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/kontrolgruppen_itkdev_dk/htdocs; git reset origin/${BRANCH_NAME} --hard'"

                // Run composer.
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/kontrolgruppen_itkdev_dk/htdocs; APP_ENV=prod composer install --no-dev -o'"

                // Run migrations.
                sh "ansible srvappkongruppe -m shell -a 'cd /data/www/kontrolgruppen_itkdev_dk/htdocs; APP_ENV=prod php bin/console doctrine:migrations:migrate --no-interaction'"

                // Copy encore assets.
                sh "ansible srvappkongruppe -m synchronize -a 'src=${WORKSPACE}/public/prod dest=/data/www/kontrolgruppen_itkdev_dk/htdocs/public/'"
            }
        }
    }
    post {
        always {
            script {
                slackNotifier(currentBuild.currentResult)
            }
        }
    }
}
