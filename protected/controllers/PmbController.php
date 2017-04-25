<?php

class PmbController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			
		);
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('create','captcha','index','import','sendmail','view','print'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('update',),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionPrint($id)
	{
		$pdf = Yii::createComponent('application.extensions.tcpdf.ETcPdf', 
                        'P', 'mm', 'A4', true, 'UTF-8');

		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		
		$pdf->AddPage();
		$pdf->SetAutoPageBreak(TRUE, 0);

		$this->layout = '';
		ob_start();
		//echo $this->renderPartial(“createnewpdf“,array(‘content’=>$content));
		
		echo $this->renderPartial('print',array(
			'model'=>$this->loadModel($id),
		));
		$data = ob_get_clean();
		ob_start();
		$pdf->writeHTML($data);

		$pdf->Output();
	}

	public function actionSendmail($mailto, $body)
	{
		$headers="From: rektorat@unida.gontor.ac.id\r\nReply-To: ".$mailto;
		mail($mailto, "Pendaftaran - UNIDA Gontor", $body,$headers);
	}

	private function actionImport()
	{
		$row = 1;
		if (($handle = fopen($_SERVER['DOCUMENT_ROOT'].'/'.Yii::app()->baseUrl."/datapmb.csv", "r")) !== FALSE) {
		  while (($hsl = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    $num = count($hsl);

		    $data = $hsl;
		    for ($c=0; $c < $num; $c++) {
		       if(empty($data[$c]))
		       	   $data[$c] = '-';
		    }


		    $pmb = new Pmb;
	    	$pmb->nama_peserta 			= $data[0];
			$pmb->tempat_lahir 			= $data[1];
			$pmb->tanggal_lahir 		= $data[2];
			$pmb->jenis_kelamin= $data[3];
			$pmb->pilihan_pertama= $data[4];
			$pmb->pilihan_kedua= $data[5];
			$pmb->pilihan_ketiga= $data[6];
			$pmb->alamat_lengkap= $data[7];
			$pmb->desa= $data[8];
			$pmb->kecamatan= $data[9];
			$pmb->kabupaten= $data[10];
			$pmb->propinsi= $data[11];
			$pmb->kodepos= $data[12];
			$pmb->telp= $data[13];
			$pmb->hp= $data[14];
			$pmb->email= $data[15];
			$pmb->pesantren= $data[16];
			$pmb->nama_pesantren= $data[17];
			$pmb->tahun_lulus= $data[18];
			$pmb->lama_pendidikan= $data[19];
			$pmb->takhassus= $data[20];
			$pmb->sd= $data[21];
			$pmb->smp= $data[22];
			$pmb->sma= $data[23];
			$pmb->nama_ayah= $data[24];
			$pmb->pendidikan_ayah= $data[25];
			$pmb->pekerjaan_ayah= $data[26];
			$pmb->penghasilan_ayah= $data[27];
			$pmb->nama_ibu= $data[28];
			$pmb->pendidikan_ibu= $data[29];
			$pmb->pekerjaan_ibu= $data[30];
			$pmb->penghasilan_ibu= $data[31];
			$pmb->pelatihan= $data[32];
			$pmb->skill= $data[33];
			$pmb->is_alumni= $data[34];
			$pmb->kampus_tujuan= $data[35];
			$pmb->rencana_studi= $data[36];
			$pmb->created = $data[37];

	        if($pmb->validate())
	        {
	        	$pmb->save();
	        }

	        else{
	        	print_r($pmb->getErrors()); 
	        }
		    // echo "<p>$num fields in line $row: <br /></p>\n";
		    $row++;
		    // for ($c=0; $c < $num; $c++) {
		    //     echo $data[$c] . "<br />\n";
		    // }
		  }
		  fclose($handle);
		}
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Pmb;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Pmb']))
		{
			$model->attributes=$_POST['Pmb'];
			if($model->save())
			{
				Yii::app()->user->setFlash('contact','Terima kasih telah mendaftar');
				$this->redirect(array('view','id'=>$model->id_pmb));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Pmb']))
		{
			$model->attributes=$_POST['Pmb'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_pmb));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	// /**
	//  * Lists all models.
	//  */
	public function actionIndex()
	{
		
		$this->render('index');
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Pmb('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Pmb']))
			$model->attributes=$_GET['Pmb'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Pmb the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Pmb::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Pmb $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='pmb-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
