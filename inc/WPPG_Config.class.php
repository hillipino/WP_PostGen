<?php
class WPPG_Config {
	public $plugin_name      = "";
	public $plugin_dir       = "";
	public $plugin_url       = "";
	public $plugin_debug     = true;
	
	public $wp_upgrade_inc   = "";
	
	public $list_per_page    = "5";
	public $data_per_page    = "200";
	
	public $tbl_import       = "wppg_import";
	public $tbl_datamap      = "wppg_datamap";
	public $tbl_format       = "wppg_format";
	public $tbl_replacetxt   = "wppg_replacetext";
	
	public $db_opt_name      = "wppg_db_version";
	public $db_version       = "1.0";
	public $db_columns       = "post_title,post_content,post_excerpt,post_name,post_content_filtered";
	public $blog_id          = "";
	public $site_id          = "";
	public $lexicon_fname    = "lexicon.txt";
	public $importFileDelim  = array('pipe'      => '|',
					 'comma'     => ',',
					 'semicolon' => ';',
					 'tab'       => "\t");
	
	public $main_slug             = "wppg_main";
	public $debug_slug   	      = "wppg_debug";
	public $import_job_slug       = "wppg_import_job";
	public $add_import_job_slug   = "wppg_add_import_job";
	public $word_replace_slug     = "wppg_word_replace";
	
	public $main_url             = "";
	public $debug_url   	     = "";
	public $import_job_url       = "";
	public $add_import_job_url   = "";
	public $word_replace_url     = "";
	public $debug_fp             = "";
	
	public $preloadMapName       = "";
	public $preloadMapCols       = array();
	
	public $stopwords            = array('a','able','about','above','according','accordingly','across','actually','after','afterwards','again','against','ago','aint','all','allow','allows','almost','alone','along','already','also','although','always','am','among','amongst','amoungst','amount','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','arent','around','as','aside','ask','asking','associated','at','available','away','awfully','b','back','be','became','because','become','becomes','becoming','been','before','beforehand','behind','being','believe','below','beside','besides','best','better','between','beyond','bill','both','bottom','brief','but','by','c','cmon','cs','call','came','can','cant','cannot','cant','cause','causes','certain','certainly','changes','clearly','co','com','come','comes','computer','con','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldnt','couldnt','course','cry','currently','d','de','definitely','describe','described','despite','detail','did','didnt','different','do','does','doesnt','doing','dont','done','down','downwards','due','during','e','each','early','edu','eg','eight','either','eleven','else','elsewhere','empty','enough','entirely','especially','et','etc','even','ever','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','far','few','fifteen','fifth','fify','fill','find','fire','first','five','followed','following','follows','for','former','formerly','forth','forty','found','four','from','front','full','further','furthermore','g','get','gets','getting','give','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadnt','happens','hardly','has','hasnt','hasnt','have','havent','having','he','hes','hello','help','hence','her','here','heres','hereafter','hereby','herein','hereupon','hers','herself','hi','high','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','id','ill','im','ive','ie','if','ignored','immediate','in','inasmuch','inc','indeed','indicate','indicated','indicates','inner','insofar','instead','interest','into','inward','is','isnt','it','itd','itll','its','its','itself','j','just','k','keep','keeps','kept','know','known','knows','l','last','late','lately','later','latter','latterly','least','less','lest','let','lets','like','liked','likely','little','long','look','looking','looks','ltd','m','made','mainly','make','many','may','maybe','me','mean','meanwhile','merely','might','mill','mine','more','moreover','most','mostly','move','much','must','my','myself','n','name','namely','nd','near','nearly','necessary','need','needs','neither','never','nevertheless','new','next','nine','no','nobody','non','none','noone','nor','normally','not','nothing','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','only','onto','or','other','others','otherwise','ought','our','ours','ourselves','out','outside','over','overall','own','p','part','particular','particularly','per','perhaps','placed','please','plus','possible','presumably','probably','provides','put','puts','q','que','quite','qv','r','rather','rd','re','really','reasonably','regarding','regardless','regards','relatively','respectively','right','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','she','shes','should','shouldnt','show','side','since','sincere','six','sixty','so','some','somebody','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','spite','still','sub','such','sup','sure','system','t','ts','take','taken','tell','ten','tends','th','than','thank','thanks','thanx','that','thats','thats','the','their','theirs','them','themselves','then','thence','there','theres','thereafter','thereby','therefore','therein','theres','thereupon','therewith','these','they','theyd','theyll','theyre','theyve','thick','thin','think','third','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','to','together','too','took','top','toward','towards','tried','tries','truly','try','trying','twelve','twenty','twice','two','u','un','under','unfortunately','unless','unlikely','until','unto','up','upon','us','use','used','useful','uses','using','usually','uucp','v','value','various','very','via','viz','vs','w','want','wants','was','wasnt','way','we','wed','well','were','weve','welcome','well','went','were','werent','what','whats','whatever','when','whence','whenever','where','wheres','whereafter','whereas','whereby','wherein','whereupon','wherever','whether','which','while','whither','who','whos','whoever','whole','whom','whose','why','will','willing','wish','with','within','without','wont','wonder','would','wouldnt','x','y','yes','yet','you','youd','youll','youre','youve','your','yours','yourself','yourselves','z','zero');
	
	function __construct() {
		
		global $wpdb;
		
		$this->plugin_name    = str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$this->plugin_name    = str_replace("/inc/", "", $this->plugin_name);
		$this->plugin_dir     = WP_PLUGIN_DIR . "/" . $this->plugin_name;
		$this->plugin_url     = WP_PLUGIN_URL . "/" . $this->plugin_name;
		$this->wp_upgrade_inc = ABSPATH . "wp-admin/includes/upgrade.php";

		$this->blog_id        = function_exists('get_current_blog_id') ? get_current_blog_id() : 1;
		$this->site_id        = function_exists('get_current_site') ? get_current_site()->id : 1;
		
		$this->tbl_import       = $wpdb->base_prefix . $this->tbl_import;
		$this->tbl_format       = $wpdb->base_prefix . $this->tbl_format;
		$this->tbl_replacetxt   = $wpdb->base_prefix . $this->tbl_replacetxt;
		$this->tbl_datamap      = $wpdb->base_prefix . $this->tbl_datamap;
		
		$this->main_url             = admin_url( 'admin.php?page='.$this->main_slug );
		$this->debug_url            = admin_url( 'admin.php?page='.$this->debug_slug );
		$this->import_job_url       = admin_url( 'admin.php?page='.$this->import_job_slug );
		$this->add_import_job_url   = admin_url( 'admin.php?page='.$this->add_import_job_slug );
		$this->word_replace_url     = admin_url( 'admin.php?page='.$this->word_replace_slug );
		
		/********* preloadMapTypes Definitions *********/
		
		// preloadMapCols - must be in order of columns in import file - set a column to blank to skip it, but do not omit it from below
		
		// for WP Tube start
		$mapName = "WP Tube";
		$this->preloadMapNames[] = $mapName;
		$this->preloadMapCols[$mapName][] = "post_title";
		$this->preloadMapCols[$mapName][] = "post_content";
		$this->preloadMapCols[$mapName][] = "thumb";
		$this->preloadMapCols[$mapName][] = "paysite_title";
		$this->preloadMapCols[$mapName][] = "paysite_url";
		$this->preloadMapCols[$mapName][] = "video_code";
		$this->preloadMapCols[$mapName][] = "duration";
		$this->preloadMapCols[$mapName][] = "category";
		// for WP Tube end
		
		// for TubeTheme start
		$mapName = "TubeTheme";
		$this->preloadMapNames[] = $mapName;
		$this->preloadMapCols[$mapName][] = "post_title";
		$this->preloadMapCols[$mapName][] = "post_content";
		$this->preloadMapCols[$mapName][] = "post_excerpt";
		$this->preloadMapCols[$mapName][] = "paysite";
		$this->preloadMapCols[$mapName][] = "paysitelink";
		$this->preloadMapCols[$mapName][] = "videoembed";
		$this->preloadMapCols[$mapName][] = "duration";
		$this->preloadMapCols[$mapName][] = "category";
		// for TubeTheme end
		
		// for TubeWaresTubeTypes start
		$mapName = "TubeWaresTubeTypes";
		$this->preloadMapNames[] = $mapName;
		$this->preloadMapCols[$mapName][] = "tubetypes";
		$this->preloadMapCols[$mapName][] = "post_title";
		$this->preloadMapCols[$mapName][] = "post_content";
		$this->preloadMapCols[$mapName][] = "video_code";
		$this->preloadMapCols[$mapName][] = "thumb";
		$this->preloadMapCols[$mapName][] = "paysite_url";
		$this->preloadMapCols[$mapName][] = "paysite_title";
		// for TubeWaresTubeTypes end
	}
} ?>