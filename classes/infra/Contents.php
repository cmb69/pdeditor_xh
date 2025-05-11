<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Pdeditor_XH.
 *
 * Pdeditor_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pdeditor_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pdeditor_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Pdeditor\Infra;

class Contents
{
    /** @var string */
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function mtime(): int
    {
        return (int) filemtime($this->filename);
    }

    public function save(): bool
    {
        return XH_saveContents();
    }
}
