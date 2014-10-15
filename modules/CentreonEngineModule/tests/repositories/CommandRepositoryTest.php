<?php
/*
 * Copyright 2005-2014 MERETHIS
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give MERETHIS
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of MERETHIS choice, provided that
 * MERETHIS also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 */


namespace Test\CentreonEngine\Repository;

use \Test\Centreon\DbTestCase;
use \Centreon\Internal\Di;
use \Centreon\Internal\Utils\Filesystem\Directory;
use \CentreonEngine\Repository\CommandRepository;

class CommandRepositoryTest extends DbTestCase
{
    protected $dataPath = '/modules/CentreonEngineModule/tests/data/json/';
    protected $tmpDir;

    public function setUp()
    {
        parent::setUp();
        $this->tmpDir = Directory::temporary('ut_', true);
    }

    public function tearDown()
    {
        if ($this->tmpDir != '' && is_dir($this->tmpDir)) {
            Directory::delete($this->tmpDir, true);
        }
        parent::tearDown();
    }

    public function testGenerate()
    {
        $fileList = array();
        $pollerId = 1;
        CommandRepository::generate($fileList, $pollerId, $this->tmpDir . '/', 'command.cfg', CommandRepository::CHECK_TYPE);
        $this->assertEquals(
            array('cfg_file' => array(
                $this->tmpDir . '/1/command.cfg'
            )), $fileList
        );
        $content = file_get_contents($this->tmpDir . '/1/command.cfg');
        /* Remove line with the generate date */
        $lines = split("\n", $content);
        $lines = preg_grep('/^#\s+Last.*#$/', $lines, PREG_GREP_INVERT);
        $content = join("\n", $lines);
        $resultContent = file_get_contents(dirname(__DIR__) . '/data/configfiles/command1.cfg');
        $this->assertEquals($resultContent, $content);
        $fileList = array();
        CommandRepository::generate($fileList, $pollerId, $this->tmpDir . '/', 'command.cfg', CommandRepository::NOTIF_TYPE);
        $this->assertEquals(
            array('cfg_file' => array(
                $this->tmpDir . '/1/command.cfg'
            )), $fileList
        );
        $content = file_get_contents($this->tmpDir . '/1/command.cfg');
        /* Remove line with the generate date */
        $lines = split("\n", $content);
        $lines = preg_grep('/^#\s+Last.*#$/', $lines, PREG_GREP_INVERT);
        $content = join("\n", $lines);
        $resultContent = file_get_contents(dirname(__DIR__) . '/data/configfiles/command2.cfg');
        $this->assertEquals($resultContent, $content);
    }
}