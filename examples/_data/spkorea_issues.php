<?php
function spkorea_issues()
{
    return array(
        'title' => 'Tickets',
        'single' => 'Ticket',
        'model' => App\Models\SpkoreaIssue::class,
        'is_top_actions' => true,
        'columns' => array(
            'si_id' => array(
                'title' => 'ID'
            ),
            'created_at' => array(
            	'title' => 'Date',
                'output' => function($value) {
                    $timezone = new DateTimeZone("Asia/Seoul");
                    $date = new DateTime($value);
                    $date->setTimezone($timezone);
                    return $date->format('Y-m-d H:i:s')." (Asia/Seoul)";
                }
            ),
            'user' => array(
                'title' => 'User',
                'relationship' => 'user',
                'select' => 'CONCAT((:table).su_name, " -  ", (:table).email)'
            ),
            'si_guest_name' => array(
                'title' => 'Guest'
            ),
            'si_type' => array(
                'title' => 'Issue Types',
                'output' => function ($value) {
                    switch ($value) {
                        case 'PA':
                            return 'Shipping weight';
	                    case 'PX':
	                    	return 'not exist part';
                        default:
                            return '';
                    }
                }
            ),
            'si_status' => array(
                'title' => 'Status',
                'output' => function ($value) {
                    switch ($value) {
                        case 'A':
                            return 'Receipt';
                        case 'I':
                            return 'Processing';
                        case 'C':
                            return 'Complete';
                        default:
                            return '';
                    }
                }
            ),
            'si_mailling' => array(
            	'title' => 'Sending mail',
	            'output' => function($value) {
            		if($value) return '<center>O</center>';
            		else return '<center>X</center>';
	            }
            ),
            'si_title' => array(
                'title' => 'Title'
            ),
            'si_desc' => array(
                'title' => 'Description'
            )

        ),
        'edit_fields' => array(
            'si_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'user' => array(
                'title' => 'Order of',
                'type' => 'relationship',
                'description' => 'Search Name or E-mail.',
                'name_field' => 'su_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(su_name, " - ", email)'),
                'editable' => function ($model) {
                    return !$model->exists;
                }
            ),
            'si_guest_email' => array(
                'title' => 'Guest Email',
            ),
            'si_guest_name' => array(
                'title' => 'Guest Name',
            ),
            'si_rel' => array(
                'title' => 'Related issue',
            ),
            'si_key' => array(
                'title' => 'Related key',
            ),
            'si_type' => array(
                'title' => 'Issue Type',
                'type' => 'enum',
                'options' => array(
                    'PA' => 'Shipping weight',
	                'PX' => 'not exist part'
                )
            ),
            'si_status' => array(
                'title' => 'Status',
                'type' => 'enum',
                'options' => array(
                    'A' => 'Receipt',
                    'I' => 'Processing',
                    'C' => 'Complete',
                )
            ),
            'si_file' => array(
                'title' => 'File',
                'type' => 'file',
                'location' => public_path().'/img/issue/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 2,
            ),
            'si_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 130,
            ),
            'si_memo' => array(
                'title' => 'Memo',
                'type' => 'textarea',
                'height' => 130,
            )

        ),
        'actions' => array(
            'go_parts' => array(
                'title' => 'go to part',
                'permission' => function($model) {
                    if($model->si_type === 'PA') {
                        return true;
                    }
                    return false;
                },
                'action' => function($model) {
                    if($model->si_type === 'PA') {
                        Session::put('issue_id', $model->si_key);
                        return redirect('/admin/spkorea_parts/'.$model->si_key);
                    }
                    return false;
                }
            ),
	        'send_mail' => array(
	        	'title' => 'send mail',
		        'messages' => array(
		        	'active' => 'Sending...',
			        'success' => 'Completed',
			        'error' => 'Failed',
		        ),
		        'permission' => function($model) {
	        		return !$model->si_mailling && ($model->si_type === 'PA') && ($model->si_status === 'C');
		        },
		        'action' => function($model) {
	        		if ($model->sendMail()) {
	        		    $model->si_mailling = true;
	        		    return $model->save();
			        }
			        return false;
		        }
	        ),
            'send_mail_not_exists_part' => array(
                'title' => 'send mail (not exists part)',
                'messages' => array(
                    'active' => 'Sending...',
                    'success' => 'Completed',
                    'error' => 'Failed',
                ),
                'permission' => function($model) {
                    return !$model->si_mailling && ($model->si_type === 'PX') && ($model->si_status === 'A');
                },
                'action' => function($model) {
                    if ($model->sendMailNotExistsPart()) {
                        $model->si_mailling = true;
                        return $model->save();
                        return true;
                    }
                    return false;
                }
            )

        ),
        'filters' => array(
            'si_id' => array(
                'title' => 'Issue ID',
                'type' => 'key'
            ),
            'user' => array(
                'title' => 'User',
                'type' => 'relationship',
                'description' => 'Name/E-mail/Phone.',
                'name_field' => 'su_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(su_name, email, su_phone)'),
            ),
        ),
        'action_permissions' => array(
            'delete' => function ($model) {
                return false;
            }
        ),
        'form_width' => 450,
    );
}
