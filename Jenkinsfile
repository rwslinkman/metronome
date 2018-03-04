node {
    stage('Checkout') {
        // Checkout code from repository
        checkout scm

        // Setup ENV for rest of the build
        String localBin = "/usr/local/bin/"
        if(!env.PATH.contains(localBin)) {
            env.PATH = "${localBin}:${env.PATH}"
        }
    }

    stage('Update dependencies') {
        // Clean up old build
        sh "composer install"
    }

    stage('Run tests') {
        // Call build with Ant to create quality results
        sh 'ant full-build'
    }

    stage('Collect analysis results') {
        // JUnit and Cobertura results for Jenkins
        step([$class: 'JUnitResultArchiver', testResults: '**/build/phpunit/*.xml'])
        step([$class: 'CloverPublisher',
              cloverReportDir: 'build/coverage',
              cloverReportFileName: 'clover.xml',
              failingTarget: [conditionalCoverage: 1, methodCoverage: 1, statementCoverage: 1],
              healthyTarget: [conditionalCoverage: 80, methodCoverage: 70, statementCoverage: 80],
              unhealthyTarget: [conditionalCoverage: 2, methodCoverage: 2, statementCoverage: 2]]
        )
        checkstyle canComputeNew: false, defaultEncoding: '', healthy: '', pattern: 'build/phpcs/phpcs.xml', unHealthy: ''
        pmd canComputeNew: false, defaultEncoding: '', healthy: '', pattern: 'build/phpmd/pmd.xml', unHealthy: ''
        dry canComputeNew: false, defaultEncoding: '', healthy: '', pattern: 'build/phpmd/pmd-cpd.xml', unHealthy: ''
        step([$class: 'XUnitBuilder',
              testTimeMargin: '3000',
              thresholdMode: 1,
              thresholds: [
                  [$class: 'FailedThreshold', failureNewThreshold: '', failureThreshold: '', unstableNewThreshold: '', unstableThreshold: ''],
                  [$class: 'SkippedThreshold', failureNewThreshold: '', failureThreshold: '', unstableNewThreshold: '', unstableThreshold: '']
              ],
              tools: [
                  [$class: 'PHPUnitJunitHudsonTestType', deleteOutputFiles: true, failIfNotNew: true, pattern: 'build/phpunit/phpunit_log.xml', skipNoTestFiles: false, stopProcessingIfError: true]
              ]
        ])
    }
}
