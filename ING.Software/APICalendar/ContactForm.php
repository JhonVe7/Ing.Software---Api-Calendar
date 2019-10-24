<!Doctype html>
<!DOCTYPE html>
<html>
<head>
	<title>Contact form</title>
	<meta charset="utf-8">
	<meta name="viewport" content="whidth=device-whidth, initial -scale=1">
</head>
<body>
	<form action="" method="POST">
		<?php
		if(isset($_POST['agendar'])){
			if($m!=''){
			?>
			<label class="control-form">Error :<?php echo $m;	?></label>
			<?php
			}elseif($id_event!=''){
				?>
				<label class="control-form">ID EVENTO :<?php echo $id_event;	?></label><br>
				<a href="<?php echo $link_event;	?>">LINK</a>
			<?php
			}
			?>
			<br>
			<button type="button" class="btn btn-primary btn-block" onclick="reload();">BACK</button>	
			</br>
		<?php
		}
		else{
		?>
		<div class="form-group">
			<div class= "row">
				<div class="col-sm-12">
					<label>Name</label>
					<div class="form-group">
						<div class="input-group">
							<input type="text" class="form-control"name="username" placeholder="entrar" autocomplete="off" />
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<label>DateTime</label>
					<div class="form-group">
						<div class="input-group date" id="datetimepicker1" data-target-input="nearest">
							<input type="text" class="form-control datetimepicker-input" name="" data-target="" name= "date_start"/>
							<div class="input-gropu-append" data-taget="#datetimepicker1" data-toggle="">
								<div class="input-group-text"><i class="fa fa-calendar"></i></div>
							</div>
						</div>
					</div>
				</div>
			
			</div>
		</div>

		<button type="submit" class="btn btn-primary btn-block" name="agregar">submit</button>
	</form>

	<?php
	$m =''; //for error
	$id_event=''; //id event created
	$link_event;
	if (isset($_POST['agendar'])) {
		
		date_default_timezone_set('America/bogota');
		inclued_once 'google-api-php-client-2.2.4/vendedor/autoland.php';

		//configurar variables de entorno
		putenv('GOOGLE_APPLICATION_CREDENTIALS=credenciales.json');
	
		$client = new google_Client();
		$client ->useApplicationDefaultCredentials();
		$client ->setScopes(['https://googleapis.com/auth/calendar']);

		//define id calendario
		 $id_calendar ='9455e4fdc385b327c2cd1f1b356fab734b4cf499';

		 $datatime_start = new DateTime($_POST['date_start']);
		 $datetime_end = new DateTime($_POST['date_start']);

		 //aumentamos una hora a la hora inicial
		 $time_end = $datetime_end->add(new DateInterval('PT1H'));

		 //datetime must be format rfc3339
		 $time_start = $datetime_start->format(\DateTime::RFC3339);
		 $time_end = $time_end->format(\DateTime::RFC3339);

		 $nombre=(isset($_POST['username']))?$_POST['username']:' xyz ';
		 try{

		 	//intanciamientos de servicio
		 	$caledarService = new Google_Service_Calendar($client);

		 	//parametros para buscar eventos en el rango de las fechas del nuevo evento
		 	$optParams = array(
		 		'oderBy' => 'startTime',
		 		'maxResults' => 20,
		 		'singleEvents' => TRUE,
		 		'timeMin' => $time_start,
		 		'timeMax' => $time_end,
		 	);

		 	//obtener eventos
		 	$events = $calendarService->events->listEvents($id_calendar,$optParams);

		 	//obtener númeto de eventos
		 	$cont_events = count($events->getItems());

		 	//crear evento si no hay eventos
		 	if($cont_events == 0){
		 		
		 		$event = new Google_Service_Calendar_Event();

		 		$event->setSummary('Cita con el paciente'. $nombre);

		 		$event->setDescription('Revisión , Tratamiento');
		 	
		 		//Fecha inicio
		 		$start = new Google_Service_Calendar_EventDateTime();
		 		$start->setDateTime($time_start);
		 		$event->setStart($start);


		 		//Fecha fin
		 		$end = new Google_Service_Calendar_EventDateTime();
		 		$end->setDateTime($time_end);
		 		$event->setStart($end);

		 		$createEvent = $calendarService->events->insert($id_calendar, $event);
		 		$id_event = $createdEvent->getid();
		 		$link_event = $createdEvent->gethtmlLink();

		 	}else{
		 		$m = 'Hay' - $cont_events - " eventos en ese rango de fechas";
		 	}
		 }catch(Google_Service_Exception $gs){

		 	$m = json_decode($gs->getMessage());
		 	$m = $m->error->message;

		 }catch(Exception $e){
		 	$m = $e->getMessage();
		 }
	}
	?>
</body>
</html>