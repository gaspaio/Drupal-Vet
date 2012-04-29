<?php
class DVet_Util_Test extends PHPUnit_Util_Test {

  public static function getName($className, $methodName = '') {
    $default = !empty($methodName) ? $methodName : $className;
    return self::getDVetAnnotation('name', $className, $methodName, $default);
  }

  public static function getDescription($className, $methodName = '') {
    return self::getDVetAnnotation('description', $className, $methodName, '');
  }

  public static function getStatusMessage($status, $className, $methodName) {
    if (is_int($status)) {
      $status = self::status2annotation($status);
    }
    return self::getDVetAnnotation($status, $className, $methodName);
  }

  public static function getWeight($className, $methodName = '') {
    $default = 1;
    $annotatedWeight = self::getDVetAnnotation('weight', $className, $methodName, $default);
    return (float) $annotatedWeight;
  }

  private static function getDVetAnnotation($annotation, $className, $methodName, $defaultValue = '') {
    $annotations = self::getDVetAnnotations($annotation, $className, $methodName, $defaultValue);
    return array_pop($annotations);
  }

  private static function getDVetAnnotations($annotation, $className, $methodName, $defaultValue = '') {
    $annotations = self::parseTestMethodAnnotations($className, $methodName);

    if (isset($annotations['method'][$annotation])) {
      return $annotations['method'][$annotation];
    }
    if (isset($annotations['class'][$annotation])) {
      return $annotations['class'][$annotation];
    }
    return array($defaultValue);
  }

  private static function status2annotation($status) {
    $statuses = self::getBaseTestRunnerStatuses();

    return isset($statuses[$status]) ?
      $statuses[$status] . 'Message' :
      FALSE;
  }

  public static function getBaseTestRunnerStatuses() {
    static $statuses;

    if (!isset($statuses)) {
      $baseRunner = new ReflectionClass('PHPUnit_Runner_BaseTestRunner');
      foreach ($baseRunner->getConstants() as $name => $value) {
        if (strpos($name, 'STATUS_') === 0) {
          $statuses[$value] = strtolower(substr($name, 7));
        }
      }
    }

    return $statuses;
  }
}
