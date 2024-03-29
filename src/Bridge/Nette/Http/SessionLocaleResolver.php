<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Http;

use Modette\Translation\Locale\LocaleResolver;
use Nette\Http\IResponse;
use Nette\Http\Session;

final class SessionLocaleResolver implements LocaleResolver
{

	public const SECTION = 'modette.translation';

	public const PARAMETER = 'locale';

	/** @var IResponse */
	private $response;

	/** @var Session */
	private $session;

	public function __construct(Session $session, IResponse $response)
	{
		$this->response = $response;
		$this->session = $session;
	}

	public function resolve(): ?string
	{
		if (!$this->session->isStarted() && $this->response->isSent()) {
			trigger_error(
				sprintf(
					'The advice of session locale resolver is required but the session has not been started and headers had been already sent. ' .
					'Either start your sessions earlier or disabled the "%s".',
					self::class
				),
				E_USER_WARNING
			);
			return null;
		}

		$hasSection = $this->session->hasSection(self::SECTION);
		if ($hasSection && ($section = $this->session->getSection(self::SECTION))->offsetExists(self::PARAMETER)) {
			return $section->offsetGet(self::PARAMETER);
		}

		return null;
	}

}
