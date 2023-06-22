<?php
/**
 * @package   panopticon
 * @copyright Copyright (c)2023-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   https://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License, version 3 or later
 */

namespace Akeeba\Panopticon\Controller\Trait;


use Akeeba\Panopticon\Model\Site;
use Awf\Utils\Ip;

defined('AKEEBA') || die;

trait AdminToolsIntegrationTrait
{
	public function admintoolsPluginDisable(): bool
	{
		$this->csrfProtection();

		$model = $this->admintoolsGetSiteModelFromRequest();

		if (empty($model))
		{
			return false;
		}

		$defaultRedirect = $this->container->router->route(
			sprintf('index.php?view=site&task=read&id=%d', $model->getId())
		);

		try
		{
			$result = $model->adminToolsPluginDisable();

			if ($result->didChange)
			{
				$config = $model->getConfig();
				$config->set('core.admintools.renamed', $result->renamed);
				$model->setFieldValue('config', $config->toString());
				$model->save();
			}

			// Redirect
			$this->setRedirectWithMessage($defaultRedirect);
		}
		catch (\Throwable $e)
		{
			$this->setRedirectWithMessage($defaultRedirect, $e->getMessage(), 'error');
		}

		return true;
	}

	public function admintoolsPluginEnable(): bool
	{
		$this->csrfProtection();

		$model = $this->admintoolsGetSiteModelFromRequest();

		if (empty($model))
		{
			return false;
		}

		$defaultRedirect = $this->container->router->route(
			sprintf('index.php?view=site&task=read&id=%d', $model->getId())
		);

		try
		{
			$result = $model->adminToolsPluginEnable();

			if ($result->didChange)
			{
				$config = $model->getConfig();
				$config->set('core.admintools.renamed', $result->renamed);
				$model->setFieldValue('config', $config->toString());
				$model->save();
			}

			// Redirect
			$this->setRedirectWithMessage($defaultRedirect);
		}
		catch (\Throwable $e)
		{
			$this->setRedirectWithMessage($defaultRedirect, $e->getMessage(), 'error');
		}

		return true;
	}

	public function admintoolsHtaccessDisable(): bool
	{
		$this->csrfProtection();

		$model = $this->admintoolsGetSiteModelFromRequest();

		if (empty($model))
		{
			return false;
		}

		$defaultRedirect = $this->container->router->route(
			sprintf('index.php?view=site&task=read&id=%d', $model->getId())
		);

		try
		{
			$model->adminToolsHtaccessDisable();

			// Redirect
			$this->setRedirectWithMessage($defaultRedirect);
		}
		catch (\Throwable $e)
		{
			$this->setRedirectWithMessage($defaultRedirect, $e->getMessage(), 'error');
		}

		return true;
	}

	public function admintoolsHtaccessEnable(): bool
	{
		$this->csrfProtection();

		$model = $this->admintoolsGetSiteModelFromRequest();

		if (empty($model))
		{
			return false;
		}

		$defaultRedirect = $this->container->router->route(
			sprintf('index.php?view=site&task=read&id=%d', $model->getId())
		);

		try
		{
			$model->adminToolsHtaccessEnable();

			// Redirect
			$this->setRedirectWithMessage($defaultRedirect);
		}
		catch (\Throwable $e)
		{
			$this->setRedirectWithMessage($defaultRedirect, $e->getMessage(), 'error');
		}

		return true;
	}

	public function admintoolsUnblockMyIp(): bool
	{
		$this->csrfProtection();

		$model = $this->admintoolsGetSiteModelFromRequest();

		if (empty($model))
		{
			return false;
		}

		$defaultRedirect = $this->container->router->route(
			sprintf('index.php?view=site&task=read&id=%d', $model->getId())
		);

		Ip::setAllowIpOverrides(true);
		$myIp = Ip::getUserIP();

		try
		{
			$model->adminToolsUnblockIP($myIp);

			// Redirect
			$this->setRedirectWithMessage($defaultRedirect);
		}
		catch (\Throwable $e)
		{
			$this->setRedirectWithMessage($defaultRedirect, $e->getMessage(), 'error');
		}

		return true;
	}

	private function admintoolsGetSiteModelFromRequest(): ?Site
	{
		$id = $this->input->getInt('id', null);

		if (empty($id))
		{
			return null;
		}

		/** @var Site $model */
		$model = $this->getModel();
		$user  = $this->container->userManager->getUser();

		$model->findOrFail($id);

		$canEditMine = $user->getId() == $model->created_by && $user->getPrivilege('panopticon.editown');

		if (!$user->authorise('panopticon.admin', $model) && !$canEditMine)
		{
			return null;
		}

		return $model;
	}
}