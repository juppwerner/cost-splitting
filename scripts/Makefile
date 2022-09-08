# Makefile for Yii project.

APP := pfea
VERSION := 1

clean:
	rm -rf ../runtime/logs/app.log
	rm -rf ../runtime/mail/*
	find ../ -name '*.php~' -exec rm {} \;
	find ../ -name '*.un~' -exec rm {} \;

dist: clean
	cd .. && \
	    git bundle create ../$(APP)-$(VERSION).bundle master && \
	    git tag -f lastbundle master

