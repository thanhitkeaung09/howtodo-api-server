<?php

declare(strict_types=1);

namespace App\Dto;

class AppleLoginData
{
    public function __construct(
        public string | null $email,
        public string  | null $name,
        public string  $social_id,
        public string | null $social_type,
        public string | null $fcm_token
    ) {
    }

    public static function of(array $data): AppleLoginData
    {
        return new AppleLoginData(
            name: $data['name'],
            email: $data['email'],
            social_id: $data['social_id'],
            social_type: $data['social_type'],
            fcm_token: $data['fcm_token']
        );
    }

    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "email" => $this->email,
            "social_id" => $this->social_id,
            "social_type" => $this->social_type,
            "fcm_token" => $this->fcm_token
        ];
    }
}
