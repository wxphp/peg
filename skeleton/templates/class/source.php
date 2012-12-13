<?php
/**
 * Objects destructor and constructor
 */
?>
BEGIN_EXTERN_C()
void php_<?=$class_name?>_free(void *object TSRMLS_DC) 
{
    zo_<?=$class_name?>* custom_object = (zo_<?=$class_name?>*) object;
    
	#ifdef USE_<?=strtoupper($extension)?>_DEBUG
	php_printf("Calling php_<?=$class_name?>_free on %s at line %i\n", zend_get_executed_filename(TSRMLS_C), zend_get_executed_lineno(TSRMLS_C));
	php_printf("===========================================\n");
	#endif
	
	if(custom_object->native_object != NULL)
	{
		#ifdef USE_<?=strtoupper($extension)?>_DEBUG
		php_printf("Pointer not null\n");
		php_printf("Pointer address %x\n", (unsigned int)(size_t)custom_object->native_object);
		#endif
		
		if(custom_object->is_user_initialized)
		{
			#ifdef USE_<?=strtoupper($extension)?>_DEBUG
			php_printf("Deleting pointer of <?=$class_name?> with delete\n");
			#endif
			
			delete custom_object->native_object;		
			custom_object->native_object = NULL;
		}
		
		#ifdef USE_<?=strtoupper($extension)?>_DEBUG
		php_printf("===========================================\n\n");
		#endif
	}
	else
	{
		#ifdef USE_<?=strtoupper($extension)?>_DEBUG
		php_printf("Not user space initialized\n");
		#endif
	}

	zend_object_std_dtor(&custom_object->zo TSRMLS_CC);
    efree(custom_object);
}

zend_object_value php_<?=$class_name?>_new(zend_class_entry *class_type TSRMLS_DC)
{
	#ifdef USE_<?=strtoupper($extension)?>_DEBUG
	php_printf("Calling php_<?=$class_name?>_new on %s at line %i\n", zend_get_executed_filename(TSRMLS_C), zend_get_executed_lineno(TSRMLS_C));
	php_printf("===========================================\n");
	#endif
	
	zval *temp;
    zend_object_value retval;
    zo_<?=$class_name?>* custom_object;
    custom_object = (zo_<?=$class_name?>*) emalloc(sizeof(zo_<?=$class_name?>));

    zend_object_std_init(&custom_object->zo, class_type TSRMLS_CC);

#if PHP_VERSION_ID < 50399
	ALLOC_HASHTABLE(custom_object->zo.properties);
    zend_hash_init(custom_object->zo.properties, 0, NULL, ZVAL_PTR_DTOR, 0);
    zend_hash_copy(custom_object->zo.properties, &class_type->default_properties, (copy_ctor_func_t) zval_add_ref,(void *) &temp, sizeof(zval *));
#else
	object_properties_init(&custom_object->zo, class_type);
#endif

    custom_object->native_object = NULL;
    custom_object->object_type = PHP_<?=strtoupper($class_name)?>_TYPE;
    custom_object->is_user_initialized = 0;

    retval.handle = zend_objects_store_put(custom_object, NULL, php_<?=$class_name?>_free, NULL TSRMLS_CC);
	retval.handlers = zend_get_std_object_handlers();
	
    return retval;
}
END_EXTERN_C()

