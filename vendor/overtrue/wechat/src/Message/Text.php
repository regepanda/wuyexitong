<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Text.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Message;

/**
 * Class Text.
 *
 * @property string $content
 */
class Text extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'text';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['content'];
}
