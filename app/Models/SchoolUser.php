<?php

namespace App\Models;

use App\Core\AbstractModel;

class SchoolUser extends AbstractModel
{
    protected string $table = "school_users";

    protected string $primaryKey = "id";

    protected array $fillable = [
        "school_id",
        "user_id",
        "shift"
    ];

    protected array $required = [
        "school_id" => "O campo escola é obrigatória",
        "user_id" => "O campo usuario é obrigatorio",
        "shift" => "O campo turno é obrigatório"
    ];

    protected bool $timestamps = true;

    protected bool $softDelete = false;

    public const MORNING = "manha";
    public const AFTERNOON = "tarde";

    public const WHOLE = "integral";

    private const SHIFTS = [
        self::MORNING,
        self::AFTERNOON,
        self::WHOLE
    ];

    public function getId(): ?int
    {
        return $this->attributes["id"];
    }

    public function setSchoolId(int $schoolId): void
    {
        $this->attributes["school_id"] = $schoolId;
    }

    public function getSchoolId(): int
    {
        return $this->attributes["school_id"];
    }

    public function setUserId(int $userId): void
    {
        $this->attributes["user_id"] = $userId;
    }
    public function getUserId(): int
    {
        return $this->attributes["user_id"];
    }
    public function setShift(?string $shift): void
    {
        $shift = $shift ?? self::WHOLE;

        if (!in_array($shift, self::SHIFTS)) {
            throw new  \InvalidArgumentException("O Turno é inválido");
        }
        $this->attributes["shift"] = $shift;
    }
    public function getShift(): ?string
    {
        return $this->attributes["shift"];
    }
    public function school(): ?School
    {
        return School::find(($this->getSchoolId()));
    }
    public function user(): ?User
    {
        return User::find($this->getUserId());
    }
    public function findByUserAndSchool(int $userId, int $school): ?self
    {
        return (new static())
            ->where("user_id", "=", $userId)
            ->where("school_id", "=", $school)
            ->first();
    }

    public static function linksByUser(int $userId): ?array
    {
        return (new static())->where("user_id", "=", $userId)->get();
    }
    public static function validateSchoolUserLinks(array $links):array
    {
        $errors = [];

        if (empty($links)) {
            return["Vincule o professor a pelo menos uma escola"];
        }

        $validSchools = [];

        foreach ($links as $link) {
            $schoolId = $link["school_id"] ?? 0 ;

            $existsSchool = School::find((int)$schoolId);

            if (!$existsSchool) {
                unset($link);
            }else{
                $validSchools[] = $link;
            }
        }

        $links = $validSchools;

        $shifts = [];
        foreach ($links as $link) {
            if(!empty($link["shift"])) {
                $shifts[] = $link["shift"];
            }
        }


        if (in_array(self::WHOLE, $shifts, true) && count($shifts)>1) {
            $errors[] = "Se o professor é do turno INTEGRAL, então ele não pode ser de outro turno";
        }


        $shiftsCount = array_count_values($shifts);

        foreach ($shiftsCount as $shift => $count) {
            if($count > 1) {
                $value = match ($shift){
                    self::MORNING => "Manhã",
                    self::AFTERNOON => "Tarde",
                    self::WHOLE => "Integral",
                };

                $errors[] = "O turno {$value} não pode ser usado em mais de uma escola!";

            }

        }
        return $errors;
    }

}