<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */
    public function grabWpSessionFiles(): array
    {
        $dir = ABSPATH . 'wp-sessions';
        if (!is_dir($dir)) {
            return [];
        }

        $files = scandir($dir);
        return array_values(array_filter($files, fn($f) => !in_array($f, ['.', '..'])));
    }

}
