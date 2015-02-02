# mwRESTscripts
Scripts to handle Mediawiki content, using the [RESTful API](http://www.mediawiki.org/wiki/API:Main_page).

These scripts depend on the [Snoopy PHP net client](http://sourceforge.net/projects/snoopy/) to simulate a browser.

```
/src - the sources
/src/wrappers - classes to call the API using PHP
/src/example.ini - an example configuration file. Copy this to create your own configuration.
/src/runImpportCSV.php - A simple PHP script, which imports a spreadsheet into a wiki page.

/test - unit tests 
/test/testdata - data used for testing
```

## Importing a spreadsheet into a wiki
The CSV importer will create a new page or add a new version to an existing page (but only if data has changed!).
Data will generally be presentaed as a table.

### Preparing the data
Before the spreadsheet can be imported, please do the following:
1. Export the spreadsheet into CSV format.
2. Make sure the CSV is in UTF-8, else convert it, e.g. using Notepad.

### Preparing the page template
The table created from the spreadsheet will be inserted into a mediawiki page. 
Pages are formated using a template stored in a local file (the Mediawiki templating system is not required)
1. To create your own page template, copy test/testdata/Test.tpl to e.g. src/page.tpl.
2. Add the newly created .tpl file to Git ignore
3. Edit the file to suit your requirements. 

### Basic usage
1. Get the [Snoopy PHP net client](http://sourceforge.net/projects/snoopy/). 
2. Copy example.ini as e.g. config.ini and edit it to suit your configuration. Add your configuration to Git ignore.
3. For a simple spreadsheet import, copy exampleBasicImportsCSV.php to e.g. runImportCSV.php
4. In the newly created file, make sure the following settings are correct:
  * The path to the Snoopy class (in the include statement)
  * The path to the CSV file
  * The path to the page template (a local file path, not a URL!)
  * The name of the page to be created
  * The path to the configuration file
  
### Advanced usage
If you require more control over the format of the wiki table being created, you can extend CSVImporter and overwrite the makeWikiTable method. 
See exampleAdvancedImportCSV.php for how to do this.

### Testing
Tests are written using PHPUnit. Please run the tests, specially if you edit the source code.
1. Get [PHP Unit](https://phpunit.de/)
2. Copy example.ini as e.g. test.ini and edit it to suit your configuration. Add your configuration to Git ignore.
4. In each test file, make sure the following settings are correct:
  * The path to the Snoopy class (in the include statement)
  * The path to the test configuration file
