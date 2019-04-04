@Library('jenkins-pipeline')_

pipeline {
    agent any
    stages {
        stage('Build and test') {
            agent {
                docker {
                    image 'itkdev/php7.2-fpm:latest' /* 7.2 is used as phan only runs with this version */
                    args '-v /var/lib/jenkins/.composer-cache:/.composer:rw'
                }
            }
            stages {
                stage('Composer') {
                    steps {
                        sh 'composer install'
                    }
                }
                stage('Deployment develop') {
                    when {
                        branch 'develop'
                    }
                    steps {
                        sh 'scripts/develop/install.sh'
                        sh 'scripts/develop/develop.sh'
                    }
                }
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
                sh 'echo "DEPLOY"'
            }
        }
        stage('Deployment production') {
            when {
                branch 'master'
                tag '*'
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