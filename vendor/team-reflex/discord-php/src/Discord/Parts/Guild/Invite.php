<?php

/*
 * This file is apart of the DiscordPHP project.
 *
 * Copyright (c) 2016-2020 David Cole <david.cole1340@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\Parts\Guild;

use Carbon\Carbon;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Part;
use Discord\Parts\User\User;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use function React\Partial\bind as Bind;

/**
 * An invite to a Channel and Guild.
 *
 * @property string     $code       The invite code.
 * @property int        $max_age    How many seconds the invite will be alive.
 * @property Guild      $guild      The guild that the invite is for.
 * @property bool       $revoked    Whether the invite has been revoked.
 * @property Carbon     $created_at A timestamp of when the invite was created.
 * @property bool       $temporary  Whether the invite is for temporary membership.
 * @property int        $uses       How many times the invite has been used.
 * @property int        $max_uses   How many times the invite can be used.
 * @property User       $inviter    The user that created the invite.
 * @property Channel    $channel    The channel that the invite is for.
 */
class Invite extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'code',
        'max_age',
        'guild',
        'revoked',
        'created_at',
        'temporary',
        'uses',
        'max_uses',
        'inviter',
        'channel',
    ];

    /**
     * Accepts the invite.
     *
     * @return PromiseInterface
     */
    public function accept(): PromiseInterface
    {
        $deferred = new Deferred();

        if ($this->revoked) {
            $deferred->reject(new \Exception('This invite has been revoked.'));

            return $deferred->promise();
        }

        if ($this->uses >= $this->max_uses) {
            $deferred->reject(new \Exception('This invite has been used the max times.'));

            return $deferred->promise();
        }

        $this->http->post("invite/{$this->code}")->then(
            Bind([$deferred, 'resolve']),
            Bind([$deferred, 'reject'])
        );

        return $deferred->promise();
    }

    /**
     * Returns the id attribute.
     *
     * @return string The id attribute.
     */
    protected function getIdAttribute(): string
    {
        return $this->code;
    }

    /**
     * Returns the invite URL attribute.
     *
     * @return string The URL to the invite.
     */
    protected function getInviteUrlAttribute(): string
    {
        return "https://discord.gg/{$this->code}";
    }

    /**
     * Returns the guild attribute.
     *
     * @return Guild      The Guild that you have been invited to.
     * @throws \Exception
     */
    protected function getGuildAttribute(): Part
    {
        return $this->factory->create(Guild::class, (array) $this->attributes['guild'], true);
    }

    /**
     * Returns the channel attribute.
     *
     * @return Channel    The Channel that you have been invited to.
     * @throws \Exception
     */
    protected function getChannelAttribute(): Part
    {
        return $this->factory->create(Channel::class, (array) $this->attributes['channel'], true);
    }

    /**
     * Returns the channel id attribute.
     *
     * @return int The Channel ID that you have been invited to.
     */
    protected function getChannelIdAttribute(): int
    {
        return $this->channel->id;
    }

    /**
     * Returns the inviter attribute.
     *
     * @return User       The User that invited you.
     * @throws \Exception
     */
    protected function getInviterAttribute(): Part
    {
        return $this->factory->create(User::class, (array) $this->attributes['inviter'], true);
    }

    /**
     * Returns the created at attribute.
     *
     * @return Carbon     The time that the invite was created.
     * @throws \Exception
     */
    protected function getCreatedAtAttribute(): Carbon
    {
        return new Carbon($this->attributes['created_at']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatableAttributes(): array
    {
        return [];
    }
}
