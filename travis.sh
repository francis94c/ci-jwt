# ENV
SPLINT_VERSION="0.0.2"
VENDOR="vendor_name"
PACKAGE="package_name"

# Move package files into a Code Igniter installation directory.
# travis-splint-{version}/applicaion/splints/{vendor_name}/{package_name}
# A Code Igniter distribution set up for PHPUnit will be download after.
created=false

for entry in ./*
do
	echo ${entry}
	if [ $created = false ]; then
		mkdir -p travis-splint-${SPLINT_VERSION}/application/splints/${VENDOR}/${PACKAGE}
		$created = true
	fi
	if [ "x$entry" != "x./phpunit.xml" ] && [ "x$entry" != "x./travis.sh" ]; then
		cp -r $entry travis-splint-${SPLINT_VERSION}/application/splints/${VENDOR}/${PACKAGE}/
		rm -rf $entry
	fi
done

# Download Code Igniter source files that work well in CLI mode with PHPUnit.
wget https://github.com/splintci/travis-splint/archive/v${SPLINT_VERSION}.tar.gz -O - | tar xz

# Dependencies
# # Example: Direct download of package archov from GitHub
# wget https://github.com/francis94c/ci-parsedown/archive/v0.0.2.tar.gz -O - | tar xz
# # Then rename and move into installation directory
# mv ci-parsedown-0.0.2 ci-parsedown
# mkdir -p travis-splint-${SPLINT_VERSION}/application/splints/francis94c/
# cp -r ci-parsedown travis-splint-${SPLINT_VERSION}/application/splints/francis94c/
# rm -rf ci-parsedown
