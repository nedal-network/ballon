<?php

namespace App\Models\Scopes;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ClientScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::user()?->hasRole(['admin', 'super_admin']) ?? false) {
            if ($model instanceof Coupon) {
                $builder->where('user_id', Auth::id());
            }
        }
    }
}
