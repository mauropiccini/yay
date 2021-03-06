# Config
PROJECT := yay
DOCKER_ENV ?= dev

.PHONY: all
all:
	#    start                Start the application
	#    stop                 Stop the application
	#    restart              Restart the application
	#    build                Build the application including development environment
	#    clean                Cleans cache files, logs
	#    cleanall             Cleans containers, images and project files including caches, logs
	#    install              Install and build the application including development environment
	#    test                 Run application tests
	#    test-coverage        Run application tests and generate code coverage
	#    qa					  Run static code analysis 
	#    default-publish      Publish demo docker image to sveneisenschmidt/yay
	#    demo-publish         Publish demo docker image to sveneisenschmidt/yay-demo
	#    enable-demo          Enable demo integration
	#    disable-demo         Remove demo integration
	#    watch-logs           Watch all log files
	#    watch-redis          Watch all redis queries

# Includes
include makedefs/*.mk

# Targets
.PHONY: restart
restart: stop start

.PHONY: start
start:
	@$(call .docker-upd,mysqldb web)
	@sleep 5
	# http://localhost:50080

.PHONY: start-attached
start-attached:
	@$(call .docker-up,mysqldb web)

.PHONY: stop
stop:
	@docker-compose -p $(PROJECT) stop || true

.PHONY: shell
shell:
	@$(call .docker-run,cli,'bash',--service-ports)

.PHONY: clean
clean: .clean-project .clean-containers

.PHONY: cleanall
cleanall: .clean-project .clean-containers .clean-images

.PHONY: install
install: build .application-install-dependencies .application-install-database

.PHONY: build
build: .docker-build-images

.PHONY: test
test: .application-test

.PHONY: test-coverage
test-coverage: .application-test-coverage

.PHONY: qa
qa: .application-qa

.PHONY: enable-demo
enable-demo: .integration-enable-demo

.PHONY: disable-demo
disable-demo: .integration-disable-demo

.PHONY: publish-default
publish-default:
	@$(call .publish,dist/docker-run.default.sh,sveneisenschmidt/yay,$(DOCKER_ENV),$(DOCKER_BRANCH))

.PHONY: publish-demo
publish-demo:
	@$(call .publish,dist/docker-run.demo.sh,sveneisenschmidt/yay-demo,$(DOCKER_ENV),$(DOCKER_BRANCH))

.PHONY: watch-logs
watch-logs: .application-watch-logs

.PHONY: watch-redis
watch-redis: .application-watch-redis
