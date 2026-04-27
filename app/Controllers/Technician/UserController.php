<?php

namespace App\Controllers\Technician;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Message;
use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requireRole(User::TECHNICIAN);
    }

    public function index(): void
    {
        $user = (new User)
            ->orderBy("name")
            ->get();

        echo $this->view->render("technician/user/index", [
            "users" => $user
        ]);

        clear_old();
    }

    public function create(): void
    {
        $schools = School::all();
        echo $this->view->render("technician/user/create", [
            "title" => "Técnico | Novo Usuário - " . APP_NAME,
            "schools" => $schools
        ]);

        clear_old();
    }

    public function edit(?array $data): void
    {
        $user = User::find($data["id"]);
        $schools = School::all();
        $userSchools = $user->schoolUserLinks();

        if (!$user) {
            Message::warning("Usuário não encontrado ou  não existe!");
            redirect("/tecnico/usuarios");
            return;
        }

        echo $this->view->render("technician/user/edit", [
            "user" => $user,
            "schools" => $schools,
            "userSchools" => $userSchools
        ]);

        clear_old();

    }

    public function store(?array $data): void
    {
        $this->validateCsrfToken($data, "/tecnico/usuarios/cadastrar");

        $newUser = new User();

        try {

            $newUser->fill([
                "name" => $data["name"],
                "email" => $data["email"],
                "password" => $data["password"],
                "document" => $data["document"] ?? null,
                "role" => $data["role"],
                "status" => $data["status"],
                "schools" => $data["schools"]
            ]);

            $errors = array_merge(
                $newUser->validate($data),
                $newUser->validateBusinessRule()
            );


            if ($data["role"] == User::TEACHER) {
                $linkErrors = SchoolUser::validateSchoolUserLinks($data["schools"]);
                $errors = array_merge($errors, $linkErrors);
            }

            if ($errors) {
                flash_old($data);
                foreach ($errors as $error) {
                    Message::warning($error);
                }
                redirect("/tecnico/usuarios/cadastrar");
            }

            $newUser->save();

            if ($newUser->getRole() === User::TEACHER) {
                $this->synchronizeSchoolUser($newUser->getId(), $data["schools"]);
            }

        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/tecnico/usuarios/cadastrar");
            return;
        }


        Message::success("Usuario cadastrado com sucesso!");
        redirect("/tecnico/usuarios");
        return;


    }

    public function update(?array $data): void
    {
//        $this->validateCsrfToken($data, "/tecnico/usuarios/editar/" . $data['id']);

        $user = User::find($data["id"]);

        if (!$user) {
            Message::warning("Esse usuário não existe!");
            redirect("/tecnico/usuarios");
            return;
        }

        try {
            $user->fill([
                "name" => $data["name"],
                "email" => $data["email"],
                "role" => $data["role"],
                "status" => $data["status"]
            ]);

            if (!empty($data["document"])) {
                $user->setDocument($data["document"]);
            }

            if (!empty($data["password"])) {
                $user->setPassword($data["password"]);
            }

            $errors = array_merge(
                $user->validate($data),
                $user->validateBusinessRule($user->getId())
            );

            if ($data["role"] == User::TEACHER) {
                $linkErrors = SchoolUser::validateSchoolUserLinks($data["schools"]);
                $errors = array_merge($errors, $linkErrors);
            }

            if ($errors) {
                flash_old($data);
                foreach ($errors as $error) {
                    Message::warning($error);
                }

                redirect("/tecnico/usuarios/editar/" . $user->getId());
            }

            $user->save();


            $this->removeAllSchoolLinks($user->getId());

            if ($user->getRole() === User::TEACHER) {
                $this->synchronizeSchoolUser($user->getId(), $data["schools"]);
            }


        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/tecnico/usuarios/editar/" . $user->getId());
            return;
        }
        Message::success("Usuario editado com sucesso!");
        redirect("/tecnico/usuarios/editar/" . $user->getId());
        return;
    }

    private function synchronizeSchoolUser(int $userId, array $links): void
    {
        $validSchools = [];

        foreach ($links as $link) {
            $schoolId = $link["school_id"] ?? 0;

            $existsSchool = School::find((int)$schoolId);

            if (!$existsSchool) {
                unset($link);
            } else {
                $validSchools[] = $link;
            }
        }


        foreach ($validSchools as $school) {

            $schoolId = $school['school_id'];
            $shift = $school['shift'];

            try {
                $newSchoolUser = new SchoolUser();
                $newSchoolUser->fill([
                    "school_id" => $schoolId,
                    "user_id" => $userId,
                    "shift" => $shift
                ]);

                $newSchoolUser->save();

            } catch (\InvalidArgumentException $invalidArgumentException) {
                throw new \InvalidArgumentException($invalidArgumentException->getMessage());
            }

        }

    }

    private function removeAllSchoolLinks(int $userId): void
    {

        $links = SchoolUser::linksByUser($userId);

        if ($links) {
            /** @var SchoolUser $link */
            foreach ($links as $link) {
                $link->delete();
            }
        }
    }

    public function destroy(?array $data): void
    {

    }
}