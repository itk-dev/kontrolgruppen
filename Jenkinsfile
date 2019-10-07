@Library('jenkins-pipeline')_

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
                agent {
                    docker {
                        image 'itkdev/php7.2-fpm:latest' /* 7.2 is used as phan only runs with this version */
                        args '-v /var/lib/jenkins/.composer-cache:/.composer:rw'
                    }
                }
                stages {
                    stage('PHP7 compatibility') {
                        steps {
                            sh 'vendor/bin/phan --allow-polyfill-parser'

                        }
                    }
                    stage('Coding standards') {
                        steps {
                            sh 'vendor/bin/phpcs --standard=phpcs.xml.dist'
                            sh 'vendor/bin/php-cs-fixer --config=.php_cs.dist fix --dry-run --verbose'
                            sh 'vendor/bin/twigcs lint templates'
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
                sh 'echo "DEPLOY"'
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
