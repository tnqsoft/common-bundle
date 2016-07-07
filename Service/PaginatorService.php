<?php

namespace TNQSoft\CommonBundle\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Request;

class PaginatorService extends Paginator
{
    /**
     * @var integer
     */
    protected $page;

    /**
     * @var integer
     */
    protected $limit;

    /**
     * @var integer
     */
    protected $maxPage;

    /**
     * @var integer
     */
    protected $totalRecord;

    /**
     * @var integer
     */
    protected $totalRecordReturned;

    /**
    * @var Container
    */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
    * Set Container
    *
    * @param Container $container
    */
    public function setContainer(Container $container) {
        $this->container = $container;
    }

    /**
    * Set Request
    *
    * @param Request $request
    */
    public function setRequest(Request $request) {
        $this->request = $request;
    }

    /**
     * __construct
     *
     * @param Dql  $dql
     * @param integer $page
     * @param integer $limit
     */
    public function __construct($dql, $page = 1, $limit = 15)
    {
        parent::__construct($dql);

        $this->setPageAndLimit($page, $limit);

        //Set limit
        $this->getQuery()
                ->setFirstResult($this->limit * ($this->page - 1))
                ->setMaxResults($this->limit);

        //Calculate
        $this->totalRecordReturned = $this->getIterator()->count();
        $this->totalRecord = $this->count();
        $this->maxPage = ceil($this->totalRecord / $this->limit);
    }

    /**
     * Get Max page
     *
     * @return integer
     */
    public function getMaxpage()
    {
        return $this->maxPage;
    }

    /**
     * Get Total Record
     *
     * @return integer
     */
    public function getTotalRecord()
    {
        return $this->totalRecord;
    }

    /**
     * Render link pagination
     *
     * @param array $listParam
     * @return string
     */
    public function renderLinks($listParam=array())
    {
        if ($this->maxPage <= 1) {
            return '';
        }

        $first = 1;
        $prev = ($this->page - 1 > 0) ? $this->page - 1 : 1;
        $next = ($this->page + 1 < $this->maxPage) ? $this->page + 1 : $this->maxPage;
        $last = $this->maxPage;
        $start = ($this->page - 1) * $this->limit;
        $end = $start + $this->totalRecordReturned;

        $classPrev = '';
        if ($this->page <= 1) {
            $classPrev = 'disabled';
        }
        $classNext = '';
        if ($this->page >= $this->maxPage) {
            $classNext = 'disabled';
        }

        $currentRoute = $this->request->get('_route');
        $router = $this->container->get('router');

        $links = '<div class="row pagination-box">';
        $links .= '<div class="col-lg-6">';
        $links .= '        '.($start + 1).'-'.$end.' trên '.$this->totalRecord.' bản ghi';
        $links .= '    </div>';
        $links .= '    <div class="col-lg-6">';
        $links .= '        <ul class="pagination pagination-sm pull-right" style="margin: 0px;">';
        $links .= '            <li class="paginate_button previous '.$classPrev.'" aria-controls="DataTables_Table_0" tabindex="0" id="DataTables_Table_0_first"><a href="'.$router->generate($currentRoute, array_merge($listParam, array('page' => $first))).'" class="'.$classPrev.'"><i class="fa fa-fast-backward"></i></a></li>';
        $links .= '            <li class="paginate_button previous '.$classPrev.'" aria-controls="DataTables_Table_0" tabindex="0" id="DataTables_Table_0_previous"><a href="'.$router->generate($currentRoute, array_merge($listParam, array('page' => $prev))).'" class="'.$classPrev.'"><i class="fa fa-backward"></i></a></li>';

        for ($i = 1; $i <= $this->maxPage; ++$i) {
            if ($i === $this->page) {
                $links .= '            <li class="paginate_button active" aria-controls="DataTables_Table_0" tabindex="0"><a href="#">'.$i.'</a></li>';
            } else {
                $links .= '            <li class="paginate_button" aria-controls="DataTables_Table_0" tabindex="0"><a href="'.$router->generate($currentRoute, array_merge($listParam, array('page' => $i))).'">'.$i.'</a></li>';
            }
        }

        $links .= '            <li class="paginate_button next '.$classNext.'" aria-controls="DataTables_Table_0" tabindex="0" id="DataTables_Table_0_next"><a href="'.$router->generate($currentRoute, array_merge($listParam, array('page' => $next))).'" class="'.$classNext.'"><i class="fa fa-forward"></i></a></li>';
        $links .= '            <li class="paginate_button next '.$classNext.'" aria-controls="DataTables_Table_0" tabindex="0" id="DataTables_Table_0_last"><a href="'.$router->generate($currentRoute, array_merge($listParam, array('page' => $last))).'" class="'.$classNext.'"><i class="fa fa-fast-forward"></i></a></li>';
        $links .= '        </ul>';
        $links .= '    </div>';
        $links .= '</div>';

        return $links;
    }

    /**
     * Set Page And Limit
     *
     * @param integer $page
     * @param integer $limit
     */
    private function setPageAndLimit($page = 1, $limit = 10)
    {
        $this->page = intval($page);
        $this->limit = intval($limit);

        if (array_key_exists('page', $_GET)) {
            $this->page = intval($_GET['page']);
        }

        if (array_key_exists('limit', $_GET)) {
            $this->limit = intval($_GET['limit']);
        }
    }
}
