<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\UserRepository;
use App\Security\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Administration des utilisateurs : liste et suppression.
 *
 * Les employés viennent du système RH : il n'y a ni création ni modification.
 */
final class UserController extends BaseController
{
    /**
     * Liste les utilisateurs.
     */
    public function index(Response $response): Response
    {
        return $this->view($response, 'admin/users', [
            'title' => 'Utilisateurs',
            'users' => (new UserRepository())->all(),
            'currentId' => Auth::user()->id ?? 0,
        ]);
    }

    /**
     * Supprime un utilisateur et ses trajets (suppression en cascade) ; on ne peut pas supprimer son propre compte.
     *
     * @param string $id Identifiant de l'utilisateur (paramètre d'URL)
     */
    public function delete(Request $request, Response $response, string $id): Response|RedirectResponse
    {
        if (!$this->csrfValid($request)) {
            return $this->abort($response, 419);
        }

        $users = new UserRepository();
        $user = $users->findById((int) $id);

        if ($user === null) {
            return $this->abort($response, 404);
        }

        if ($user->id === (Auth::user()->id ?? 0)) {
            return $this->redirect(
                '/admin/utilisateurs',
                'danger',
                'Vous ne pouvez pas supprimer votre propre compte.',
            );
        }

        $users->delete($user->id);

        return $this->redirect('/admin/utilisateurs', 'success', 'Utilisateur supprimé.');
    }
}
