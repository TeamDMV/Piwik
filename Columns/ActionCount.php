<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikNotifiy\Columns;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Plugin\Dimension\ActionDimension;
use Piwik\Plugin\Segment;
use Piwik\Tracker\ActionPageview;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Tracker\Action;

use Piwik\Plugins\PiwikNotifiy\Helper;
use Piwik\Plugins\PiwikNotifiy\AsyncRequest;

/**
 * This example dimension recognizes a new tracking url parameter that is supposed to save the keywords that were used
 * on a certain page. Please note that dimension instances are usually cached during one tracking request so they
 * should be stateless (meaning an instance of this dimension will be reused if requested multiple times).
 *
 * See {@link http://developer.piwik.org/api-reference/Piwik/Plugin\Dimension\ActionDimension} for more information.
 */
class ActionCount extends ActionDimension
{
    /**
     * This will be the name of the column in the log_link_visit_action table if a $columnType is specified.
     * @var string
     */
    protected $columnName = 'action_count';
    
    //private $new_action_url = "http://localhost:8000/api/v1/piwik/new/action/";
    private $new_action_url = "http://carderockllc.com/api/v1/piwik/new/action/";


    /**
     * If a columnType is defined, we will create this a column in the MySQL table having this type. Please make sure
     * MySQL will understand this type. Once you change the column type the Piwik platform will notify the user to
     * perform an update which can sometimes take a long time so be careful when choosing the correct column type.
     * @var string
     */
    protected $columnType = 'INTEGER DEFAULT 0';

    /**
     * The name of the dimension which will be visible for instance in the UI of a related report and in the mobile app.
     * @return string
     */
    public function getName()
    {
        return Piwik::translate('PiwikNotifiy_ActionCount');
    }

    /**
     * By defining one or multiple segments a user will be able to filter their visitors by this column. For instance
     * show all actions only considering users having more than 10 achievement points. If you do not want to define a
     * segment for this dimension just remove the column.
     */
    protected function configureSegments()
    {
        $segment = new Segment();
        $segment->setSegment('keywords');
        $segment->setCategory('General_Actions');
        $segment->setName('PiwikNotifiy_ActionCount');
        $segment->setAcceptedValues('Here you should explain which values are accepted/useful: Any word, for instance MyKeyword1, MyKeyword2');
        $this->addSegment($segment);
    }

    /**
     * This event is triggered before a new action is logged to the log_link_visit_action table. It overwrites any
     * looked up action so it makes usually no sense to implement both methods but it sometimes does. You can assign
     * any value to the column or return boolan false in case you do not want to save any value.
     *
     * @param Request $request
     * @param Visitor $visitor
     * @param Action $action
     *
     * @return mixed|false
     */
    public function onNewAction(Request $request, Visitor $visitor, Action $action)
    {
        $idsite = $visitor->getVisitorColumn('idsite');
        $idvisitor = $visitor->getVisitorColumn('idvisitor');
        //$idvisitor = base_convert(bin2hex($idvisitor), 16, 10);
        $idvisitor = bin2hex($idvisitor);
        $r = new AsyncRequest($this->existing_visit_url, array(), array("idvisitor"=>$idvisitor, 'idsite'=>$idsite ), 'post');
        $r->start();

        if (!($action instanceof ActionPageview)) {
            // save value only in case it is a page view.
            return false;
        }

        $value = Common::getRequestVar('my_page_keywords', false, 'string', $request->getParams());

        if (false === $value) {
            return $value;
        }

        $value = trim($value);

        return substr($value, 0, 255);
    }

    /**
     * If the value you want to save for your dimension is something like a page title or page url, you usually do not
     * want to save the raw value over and over again to save bytes in the database. Instead you want to save each value
     * once in the log_action table and refer to this value by its ID in the log_link_visit_action table. You can do
     * this by returning an action id in "getActionId()" and by returning a value here. If a value should be ignored
     * or not persisted just return boolean false. Please note if you return a value here and you implement the event
     * "onNewAction" the value will be probably overwritten by the other event. So make sure to implement only one of
     * those.
     *
     * @param Request $request
     * @param Action $action
     *
     * @return false|mixed
    public function onLookupAction(Request $request, Action $action)
    {
        if (!($action instanceof ActionPageview)) {
            // save value only in case it is a page view.
            return false;
        }

        $value = Common::getRequestVar('my_page_keywords', false, 'string', $request->getParams());

        if (false === $value) {
            return $value;
        }

        $value = trim($value);

        return substr($value, 0, 255);
    }
     */

    /**
     * An action id. The value returned by the lookup action will be associated with this id in the log_action table.
     * @return int
    public function getActionId()
    {
        return Action::TYPE_PAGE_URL;
    }
     */
}
