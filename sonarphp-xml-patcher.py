#!/usr/bin/env python
import xml.etree.ElementTree as ET

et = ET.parse('build/logs/phpunit/junit.xml')
root = et.getroot()

for mastersuites in root:
    for suite in mastersuites:
        if not "file" in suite.attrib:
            continue
        filename = suite.attrib['file']
        for subsuite in suite:
            if subsuite.tag != "testsuite":
                continue
            if not "file" in subsuite.attrib:
                subsuite.attrib['file'] = filename

et.write('build/logs/phpunit/junit.xml')
