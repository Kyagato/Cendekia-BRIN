<?php

namespace App\Http\Requests;

class StoreKnowledgeRequest extends KnowledgeRequest
{
    protected function isUpdate(): bool
    {
        return false;
    }
}
