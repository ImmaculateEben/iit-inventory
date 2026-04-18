<?php

namespace App\Support;

trait RemembersFormState
{
    abstract protected function formStateFields(): array;

    protected function getFormSessionKey(): string
    {
        return 'form_state_' . static::class;
    }

    public function mountRemembersFormState(): void
    {
        $saved = session($this->getFormSessionKey());

        if (is_array($saved)) {
            foreach ($this->formStateFields() as $field) {
                if (array_key_exists($field, $saved) && property_exists($this, $field)) {
                    $this->$field = $saved[$field];
                }
            }
        }
    }

    public function dehydrateRemembersFormState(): void
    {
        $state = [];

        foreach ($this->formStateFields() as $field) {
            if (property_exists($this, $field)) {
                $state[$field] = $this->$field;
            }
        }

        session()->put($this->getFormSessionKey(), $state);
    }

    protected function clearFormState(): void
    {
        session()->forget($this->getFormSessionKey());
    }
}
