<?php

/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.5.
 *
 * @see       https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 *
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 - 2020 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license   https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

/**
 * OAuth - OAuth2 authentication wrapper class.
 *
 * NOTE: This is a stub class. To use OAuth2 authentication with PHPMailer,
 * install the league/oauth2-client package via Composer:
 *   composer require league/oauth2-client
 *
 * This project uses standard SMTP authentication (username/password),
 * so this file is not needed and is kept as a placeholder only.
 */
class OAuth implements OAuthTokenProvider
{
    /**
     * Generate a base64-encoded OAuth token.
     * Not implemented - use standard SMTP authentication instead.
     *
     * @return string
     */
    public function getOauth64()
    {
        return '';
    }
}
