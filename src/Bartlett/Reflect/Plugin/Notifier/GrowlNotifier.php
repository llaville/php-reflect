<?php
/**
 * Notify application events via Growl
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @link     http://growl.laurent-laville.org/
 * @link     http://pear.php.net/package/Net_Growl
 */

namespace Bartlett\Reflect\Plugin\Notifier;

use Bartlett\Reflect\Events;

use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'Net/Growl/Autoload.php';

/**
 * Notifies events via a Growl (Mac or Windows, but not Linux)
 *
 * Requires PEAR::Net_Growl package

 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha3+1
 */
class GrowlNotifier implements NotifierInterface
{
    /**
     * Net_Growl instance
     * @var object
     */
    private $growl;

    /**
     * Message format with predefined place holders
     * @var string
     */
    private $format = "%message%\n\n%profile%";

    /**
     * Class constructor
     *
     * @param string $application   (optional) Identify an application by a string
     * @param array  $notifications (optional) Options to configure the
     *                               notification channels
     * @param string $password      (optional) Password to protect your Growl client
     *                               for notification spamming
     * @param array  $options       (optional) Options to configure the Growl comm.
     *                               Choose either UDP or GNTP protocol,
     *                               host URL, and more ...
     */
    public function __construct($application = 'phpReflect', $notifications = array(),
        $password = '', $options = array('protocol' => 'gntp')
    ) {
        $notifications = array_merge(
            // default notifications
            array(
                Events::PROGRESS => array('enabled' => false),
                Events::SUCCESS  => array('enabled' => false),
                Events::ERROR    => array('enabled' => true, 'sticky' => true),
                Events::COMPLETE => array('enabled' => true, 'sticky' => true),
            ),
            // custom notifications
            $notifications
        );

        $this->growl = \Net_Growl::singleton(
            $application, $notifications, $password, $options
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setMessageFormat($format)
    {
        if (is_string($format) && !empty($format)) {
            $this->format = $format;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessageFormat()
    {
        return $this->format;
    }

    /**
     * {@inheritDoc}
     */
    public function notify(GenericEvent $event)
    {
        try {
            $this->growl->register();

            $name = $event['eventname'];

            $notifications = $this->growl->getApplication()->getGrowlNotifications();

            $this->growl->publish(
                $name,
                $this->growl->getApplication()->getGrowlName(),
                $event['formatted'],
                $notifications[$name]
            );

        } catch (\Net_Growl_Exception $e) {
            return false;
        }

        return true;
    }
}
