<?php

namespace App\Models;

use App\Contracts\JwtSubjectInterface;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $username
 * @property string|null $bio
 * @property string|null $image
 * @property-read Collection|Article[] $articles
 * @property-read int|null $articles_count
 * @property-read Collection|User[] $authors
 * @property-read int|null $authors_count
 * @property-read Collection|Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read Collection|Article[] $favorites
 * @property-read int|null $favorites_count
 * @property-read Collection|User[] $followers
 * @property-read int|null $followers_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereBio($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereImage($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @mixin Eloquent
 */
class User extends Authenticatable implements JwtSubjectInterface
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Regular expression for username.
     */
    public const REGEX_USERNAME = '/^[\pL\pM\pN._-]+$/u';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'bio',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJwtIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Determine if user is following an author.
     *
     * @param User $author
     * @return bool
     */
    public function following(User $author): bool
    {
        return $this->authors()
            ->whereKey($author->getKey())
            ->exists();
    }

    /**
     * Determine if author followed by a user.
     *
     * @param User $follower
     * @return bool
     */
    public function followedBy(User $follower): bool
    {
        return $this->followers()
            ->whereKey($follower->getKey())
            ->exists();
    }

    /**
     * The authors that the user follows.
     *
     * @return BelongsToMany<User>
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'author_follower', 'author_id', 'follower_id');
    }

    /**
     * The followers of the author.
     *
     * @return BelongsToMany<User>
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'author_follower', 'follower_id', 'author_id');
    }

    /**
     * Get the comments of the user.
     *
     * @return HasMany<Comment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    /**
     * Get user written articles.
     *
     * @return HasMany<Article>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Get user favorite articles.
     *
     * @return BelongsToMany<Article>
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_favorite');
    }
}
