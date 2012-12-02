/*
 * @author <?=$authors?>
 * @contributors <?=$contributors?>
 * 
 * @license 
 * This file is part of <?=$extension?> check the LICENSE file for information.
 * 
 * @description
 * Main start point for the <?=$extension?> php extension
 * 
 * @note
 * This file is auto-generated by PEG.
*/

#include "php_<?=$extension?>.h"
#include "functions.h"

/**
 * To enable inclusion of class methods tables entries code 
 * on the generated headers
 */
#define <?=strtoupper($extension)?>_INCLUDE_METHOD_TABLES

/**
 * Space reserved for the zend_class_entry declaration of each class
 * as its include files
 */
<?=$entries?>

/**
 * Register global objects as constants
 */
BEGIN_EXTERN_C()
void <?=$extension?>_register_object_constant(const char *name, uint name_len, zval object, int flags, int module_number TSRMLS_DC)
{
	zend_constant c;
	
	c.value = object;
	c.value.type = IS_OBJECT;
	c.flags = flags;
	c.name = zend_strndup(name, name_len-1);
	c.name_len = name_len;
	c.module_number = module_number;
	zend_register_constant(&c TSRMLS_CC);
}
END_EXTERN_C()

/**
 * Custom zend_method_call function to call methods with more than 2 parameters
 */
BEGIN_EXTERN_C()
int <?=$extension?>_call_method(zval **object_pp, zend_class_entry *obj_ce, zend_function **fn_proxy, const char *function_name, int function_name_len, zval **retval_ptr_ptr, int param_count, zval*** params TSRMLS_DC)
{
	/*First check the method is callable*/
	zval* method_to_call;
	zend_fcall_info_cache fcc;
	int is_callable = SUCCESS;
	
	if(!*fn_proxy)
	{
		MAKE_STD_ZVAL(method_to_call);
	
		array_init(method_to_call);
		add_next_index_zval(method_to_call, *object_pp);
		add_next_index_stringl(method_to_call, (char*) function_name, function_name_len, 0);
	
		if(!zend_is_callable_ex(method_to_call, NULL, 0, NULL, NULL, &fcc, NULL TSRMLS_CC))
		{
			is_callable = FAILURE;
		}
		
		efree(method_to_call);
		
		if(is_callable == FAILURE)
			return FAILURE;
	}
	
	int result;
	zend_fcall_info fci;
	zval z_fname;
	zval *retval;
	HashTable *function_table;

	fci.size = sizeof(fci);
	/*fci.function_table = NULL; will be read form zend_class_entry of object if needed */
	fci.object_ptr = object_pp ? *object_pp : NULL;
	fci.function_name = &z_fname;
	fci.retval_ptr_ptr = retval_ptr_ptr ? retval_ptr_ptr : &retval;
	fci.param_count = param_count;
	fci.params = params;
	fci.no_separation = 1;
	fci.symbol_table = NULL;

	if (!fn_proxy && !obj_ce) {
		/* no interest in caching and no information already present that is
		 * needed later inside zend_call_function. */
		ZVAL_STRINGL(&z_fname, function_name, function_name_len, 0);
		fci.function_table = !object_pp ? EG(function_table) : NULL;
		result = zend_call_function(&fci, NULL TSRMLS_CC);
	} else {
		zend_fcall_info_cache fcic = fcc;

		fcic.initialized = 1;
		if (!obj_ce) {
			obj_ce = object_pp ? Z_OBJCE_PP(object_pp) : NULL;
		}
		if (obj_ce) {
			function_table = &obj_ce->function_table;
		} else {
			function_table = EG(function_table);
		}
		if (!fn_proxy || !*fn_proxy) {
			if (fn_proxy) {
				*fn_proxy = fcic.function_handler;
			}
		} else {
			fcic.function_handler = *fn_proxy;
		}
		fcic.calling_scope = obj_ce;
		if (object_pp) {
			fcic.called_scope = Z_OBJCE_PP(object_pp);
		} else if (obj_ce &&
		           !(EG(called_scope) &&
		             instanceof_function(EG(called_scope), obj_ce TSRMLS_CC))) {
			fcic.called_scope = obj_ce;
		} else {
			fcic.called_scope = EG(called_scope);
		}
		fcic.object_ptr = object_pp ? *object_pp : NULL;
		result = zend_call_function(&fci, &fcic TSRMLS_CC);
	}
	if (result == FAILURE) {
		/* error at c-level */
		if (!obj_ce) {
			obj_ce = object_pp ? Z_OBJCE_PP(object_pp) : NULL;
		}
		if (!EG(exception)) {
			zend_error(E_CORE_ERROR, "Couldn't execute method %s%s%s", obj_ce ? obj_ce->name : "", obj_ce ? "::" : "", function_name);
		}
	}
	if (!retval_ptr_ptr) {
		if (retval) {
			zval_ptr_dtor(&retval);
		}
		return FAILURE;
	}
	return SUCCESS;
}
END_EXTERN_C()

/**
 * Global functions table entry used on the module initialization code
 */
static zend_function_entry php_<?=$extension?>_functions[] = {
	/**
	 * Space reserved for the addition to functions table of
	 * autogenerated functions
	 */
	<?=$functions_table?>

	PHP_FE_END //Equivalent to { NULL, NULL, NULL, 0, 0 } at time of writing on PHP 5.4
};

/**
 * Initialize global objects and resources
 */
PHP_RINIT_FUNCTION(php_<?=$extension?>)
{
	static int objects_intialized = 0;
	
	/**
	 * Space reserved for the initialization of global object 
	 * constants, since the php engine doesnt initializes the object
	 * store prior to calling extensions MINIT function.
	 */
	 
	if(objects_intialized < 1)
	{
	 
		<?php print $object_constants ?>
		
		objects_intialized = 1;
	}
		
    return SUCCESS;
}

PHP_MINIT_FUNCTION(php_<?=$extension?>)
{
    zend_class_entry ce; /* Temporary variable used to initialize class entries */
	
	/**
	 * Space reserved for the initialization of autogenerated classes,
	 * class enumerations and global constants
	 */
	  
	<?php print $classes ?>
	
    return SUCCESS;
}

/**
 * UnInitialize resources
 */
PHP_MSHUTDOWN_FUNCTION(php_<?=$extension?>)
{
    return SUCCESS;
}

/**
 * TODO: Automate the process of updating versions number
 * Show version information to phpinfo()
 */
PHP_MINFO_FUNCTION(php_<?=$extension?>)
{
	php_info_print_table_start();
	php_info_print_table_header(2, PHP_<?=strtoupper($extension)?>_EXTNAME, "enabled");
	php_info_print_table_row(2, "Extension Version", PHP_<?=strtoupper($extension)?>_EXTVER);
	php_info_print_table_end();
}


/**
 * Declaration of <?=$extension?> module
 */
zend_module_entry <?=$extension?>_module_entry = {
    STANDARD_MODULE_HEADER,
    PHP_<?=strtoupper($extension)?>_EXTNAME,
    php_<?=$extension?>_functions, 				/* Functions (module functions) */
    PHP_MINIT(php_<?=$extension?>),				/* MINIT (module initialization function) */
    PHP_MSHUTDOWN(php_<?=$extension?>),			/* MSHUTDOWN (module shutdown function) */
    PHP_RINIT(php_<?=$extension?>),				/* RINIT (request initialization function) */
    NULL, 										/* RSHUTDOWN (request shutdown function) */
    PHP_MINFO(php_<?=$extension?>),				/* MINFO (module information function) */
    PHP_<?=strtoupper($extension)?>_EXTVER,
    STANDARD_MODULE_PROPERTIES
};

/**
 * Declare get_module function for <?=$extension?> called by PHP runtime
 */
#ifdef COMPILE_DL_<?=strtoupper($extension)?>
BEGIN_EXTERN_C()
ZEND_GET_MODULE(<?=$extension?>) /* Here the extension name is resolved to <?=extension?>_module_entry */
END_EXTERN_C()
#endif