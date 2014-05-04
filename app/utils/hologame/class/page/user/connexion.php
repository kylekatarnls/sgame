<?php

namespace Hologame;

class Page°User°Connexion extends Page°User
{
	protected $auto;
	public function main()
	{
		if(get_post('action') === 'sign')
		{
			foreach([
				'name',
				'password',
				'password2',
				'email',
				'email2'
			] as $key)
			{
				if($this->cUser->isError('signIn', $key))
				{
					µ($this->§('#'.$key)->parent()->error());
					switch($key)
					{
						case 'name':
							$error = $this->cUser->isError('duplicate') ?
								s('Identifiant déjà pris'):
								s('Identifiant invalide');
							break;
						case 'email':
							$error = $this->cUser->isError('duplicate') ?
								s('Adresse e-mail déjà prise'):
								s('Adresse e-mail invalide');
							break;
						case 'email2':
							$error = s('Les 2 adresses e-mail sont différentes');
							break;
						case 'password':
							$error = s('Le mot de passe est trop court');
							break;
						case 'password2':
							$error = s('Les 2 mots de passe sont différents');
							break;
					}
					$this->addData('error', $this->hError($error));
				}
			}
			if($this->cUser->isError('signIn', 'password2'))
			{
				$this->addData('error', $this->hError(
					$this->cSecurity°Antibruteforce($this->cUser->errorInfo('bruteForceKey'))->getText($this->cUser->errorInfo('errorName'))
				));
			}
		}
		else
		{
			switch($this->cUser->errorInfo('logIn'))
			{
				case 'failed':
					$this->addData('error', $this->hError(
						s('Identifiant ou mot de passe incorrect'),
						p('{number} tentative restante', '{number} tentatives restantes', $this->cUser->errorInfo('count'))
					));
					break;
				case 'bruteForce':
					$this->addData('error', $this->hError(
						$this->cSecurity°Antibruteforce($this->cUser->errorInfo('bruteForceKey'))->getText($this->cUser->errorInfo('errorName'))
					));
					break;
			}
		}
		$connexion = s('Connexion');
		$inscription = s('Inscription');
		$this->setDataIfNot('page_title', $connexion.' / '.$inscription);
		$signInBlockId = 'sign-in-block';
		$logSubmitId = 'log-submit';
		$animation1 = ['slideUp', 'slideDown'];
		$animation2 = ['fadeOut', 'fadeIn'];
		$hide = $this->§('#'.$signInBlockId)
				->{$animation1[0]}('fast').
			$this->§('#'.$logSubmitId)
				->parent()
				->find('.ui-btn-text')
				->{$animation2[0]}(Raw('function (){
					$(this).text('.json_encode($connexion).').'.$animation2[1].'()
				}'));
		$hide = Html::htmlAttributes([
			'onclick' => $hide,
			'onfocus' => $hide,
			'onblur' => $hide
		]);
		$show = $this->§('#'.$signInBlockId)
				->{$animation1[1]}('fast').
			$this->§('#'.$logSubmitId)
				->parent()
				->find('.ui-btn-text')
				->{$animation2[0]}(Raw('function (){
					$(this).text('.json_encode($inscription).').'.$animation2[1].'()
				}'));
		$show = Html::htmlAttributes([
			'onclick' => $show,
			'onfocus' => $show,
			'onblur' => $show
		]);
		$logInMode = (get_post('action') !== 'sign');
		$form = $this->form
			->secure()
			->autocomplete(false)
			->radio(post('action', [
				'default' => 'log',
				'gRadio' => [
					[
						'id' => 'action-log',
						'value' => 'log',
						'label' => $connexion,
						'field' => $hide,
						'text' => $hide
					],[
						'id' => 'action-sign',
						'value' => 'sign',
						'label' => $inscription,
						'field' => $show,
						'text' => $show
					]
				]
			]))
			->text(post('name', [
				'placeholder' => s('Identifiant')
			]),post('password', [
				'placeholder' => s('Mot de passe'),
				'type' => 'password'
			]))
			->raw('<div id="'.$signInBlockId.'">')
			->text(post('password2', [
				'placeholder' => s('Confirmation du mot de passe'),
				'type' => 'password'
			]),post('email', [
				'placeholder' => s('Adresse e-mail')
			]),post('email2', [
				'placeholder' => s("Confirmation de l'adresse e-mail")
			]))
			->raw('</div>');
		if($this->auto === null)
		{
			$form	->checkbox(post('auto-log', [
					'gCheckbox' => [[
						'name' => 'auto',
						'label' => s('Rester connecté'),
						'checked' => (get_post('auto', true, 'bool') ? 'checked' : '')
					]]
				]));
		}
		else if($this->auto === true)
		{
			$form	->hidden([
					'name' => 'auto-log',
					'value' => 'on'
				]);
		}
		$form	->submit([
				'id' => $logSubmitId,
				'value' => ($logInMode ? $connexion : $inscription)
			])
			->style([
				'text-align' => 'center'
			]);
		$form->onsubmit = $this->js->loaderText(Raw('$("#action-log").is(":checked") ? "'.s("Connexion en cours").'" : "'.s("Vérification des données en cours").'"'));
		$this->setData('page', $this->getData('error', '').$form);
		if($logInMode)
		{
			µ($this->§('#'.$signInBlockId)->{$animation1[0]}(0));
		}
	}
}

?>