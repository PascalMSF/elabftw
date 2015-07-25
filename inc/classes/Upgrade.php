<?php
/********************************************************************************
*                                                                               *
*   Copyright 2012 Nicolas CARPi (nicolas.carpi@gmail.com)                      *
*   http://www.elabftw.net/                                                     *
*                                                                               *
********************************************************************************/

/********************************************************************************
*  This file is part of eLabFTW.                                                *
*                                                                               *
*    eLabFTW is free software: you can redistribute it and/or modify            *
*    it under the terms of the GNU Affero General Public License as             *
*    published by the Free Software Foundation, either version 3 of             *
*    the License, or (at your option) any later version.                        *
*                                                                               *
*    eLabFTW is distributed in the hope that it will be useful,                 *
*    but WITHOUT ANY WARRANTY; without even the implied                         *
*    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR                    *
*    PURPOSE.  See the GNU Affero General Public License for more details.      *
*                                                                               *
*    You should have received a copy of the GNU Affero General Public           *
*    License along with eLabFTW.  If not, see <http://www.gnu.org/licenses/>.   *
*                                                                               *
********************************************************************************/
namespace Elabftw\Elabftw;

use \Exception;
use \ZipArchive;

class Upgrade extends Update
{
    private $zipPath = ELAB_ROOT . 'uploads/tmp/latest.zip';


    /*
     * Upgrade the install by downloading latest zip, extracting it and copying files.
     *
     */
    public function __construct()
    {
        if (!$this->checkIsWritable()) {
            throw new Exception('Cannot write to installation directory. Fix permissions to use auto upgrade feature.');
        }
        $this->enableMaintenanceMode();
        $this->getUpdatesIni();
        $this->getLatestZip();
        if (!$this->checksumZip()) {
            throw new Exception('Cannot validate zip archive!');
        }
        $this->extractZip();
        $this->disableMaintenanceMode();
    }

    /*
     * Make sure we can actually write to the install directory
     *
     */
    private function checkIsWritable()
    {
        return is_writable(ELAB_ROOT);
    }

    private function enableMaintenanceMode()
    {
        file_put_contents(ELAB_ROOT . 'maintenance', '1');
    }

    private function disableMaintenanceMode()
    {
        if (file_exists(ELAB_ROOT . 'maintenance')) {
            unlink(ELAB_ROOT . 'maintenance');
        }
    }

    /*
     * Download the latest zip archive.
     *
     */
    private function getLatestZip()
    {
        $this->get($this->url, $this->zipPath);
    }

    /*
     * Verify integrity of zip archive with sha512.
     *
     */
    private function checksumZip()
    {
        $hash = hash_file('sha512', $this->zipPath);
        return $hash === $this->sha512;
    }

    /*
     * Extract the archive.
     * FIXME
     *
     */
    private function extractZip()
    {
        $zip = new \ZipArchive;
        if ($zip->open($this->zipPath)) {
            // $i starts with 1 because the first entry is just the directory containing everything
            for ($i = 1; $i < $zip->numFiles; $i++) {
                //$zip->extractTo(ELAB_ROOT, array(substr($zip->getNameIndex($i), strpos($zip->getNameIndex($i), '/') + 1)));
            }
            $zip->close();
        }
    }
}