<?php

class Comment_Model extends Model
{

    static $table = 'comments';
	function __construct()
	{
		parent::__construct();
	}

	public function userList($limit=null, $page = null, $sort = null)
	{

		$limit = (int) $limit;
		$start = ($page - 1) * $limit;

        $sthTotal = $this->db->prepare('select count(id) as total from ' . self::$table);
        $sthTotal->execute();
        $arrTotal = $sthTotal->fetchAll();
        $data['total'] = $arrTotal[0]['total'];
        $sort = $sort ?: 'id';

        $sth = $this->db->prepare('select id, name, email, text, status from '.self::$table.' order by '.$sort.' LIMIT :start, :limit');

	    $sth->bindValue(":start", $start, PDO::PARAM_INT);
        $sth->bindValue(":limit", $limit, PDO::PARAM_INT);

        $sth->execute();
        $data['tasks'] = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($data['tasks'][0])) {
            foreach ($data['tasks'][0] as $key => $value) {
                $data['columns'][] = $key;
            }
        }

        $data['limit'] = $limit;
        $data['page'] = $page;
        $data['pages'] = ceil($data['total']/3);
        $data['Previous'] = $page == 1 ? $data['total'] : $page - 1;
        $data['Next'] = $page == $data['pages'] ? $data['pages'] : $page + 1;

        return $data;
	}

    /**
     * @throws Exception
     */
    public function create($data)
    {
        $count = 0;
       // var_dump($data);
       // exit();
        if (!empty($data)) {
            $sth = $this->db->prepare('insert into '.self::$table.' (name, email, text) values (:name, :email, :text)');
            $sth->execute(array(
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':text' => $data['text']
            ));
            $count = $sth->rowCount();
        }

        if ($count > 0){
			echo '<script type="text/javascript">
						window.location = "../comment";
						alert("Task added successful!");
				  </script>';
		} else {
			 //show error
            throw new Exception('Data was not insert into table');
			//header('location: ../error');
		}

	}
}