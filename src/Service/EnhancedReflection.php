<?php declare(strict_types=1);

namespace Safebeat\Service;

final class EnhancedReflection extends \ReflectionClass
{
    private $useStatements;

    public function getUseStatements()
    {
        return $this->useStatements ?? $this->parseStatements();
    }

    public function hasUseStatement($class) {
        if (null === $this->useStatements) {
            $this->parseStatements();
        }

        if (false !== $this->checkUse($class)) {
            return $class;
        }

        $searched = $class;
        while(false !== stripos($searched, '\\')) {
            $searched = substr($searched, 0, - strlen(substr(strrchr($searched,'\\'), 0)));

            if (false !== $this->checkUse($searched)) {
                return $searched;
            }
        }

        return false;
    }

    private function checkUse($class)
    {
        if (false !== $search = array_search($class, array_column($this->useStatements, 'class'))) {
            return $search;
        }

        if (false !== $search = array_search($class, array_column($this->useStatements, 'as'))) {
            return $search;
        }

        return false;
    }

    private function parseStatements()
    {
        if (!$this->isUserDefined()) {
            throw new \InvalidArgumentException('Cannot parse statements for not user-defined classes');
        }

        return $this->useStatements = $this->tokenizeSource($this->readFileSource());
    }

    private function tokenizeSource($source) {
        $tokens = token_get_all($source);
        $builtNamespace = '';
        $buildingNamespace = false;
        $matchedNamespace = false;
        $useStatements = [];
        $record = false;
        $currentUse = [
            'class' => '',
            'as' => ''
        ];
        foreach ($tokens as $token) {
            if ($token[0] === T_NAMESPACE) {
                $buildingNamespace = true;
                if ($matchedNamespace) {
                    break;
                }
            }
            if ($buildingNamespace) {
                if ($token === ';') {
                    $buildingNamespace = false;
                    continue;
                }
                switch ($token[0]) {
                    case T_STRING:
                    case T_NS_SEPARATOR:
                        $builtNamespace .= $token[1];
                        break;
                }
                continue;
            }
            if ($token === ';' || !is_array($token)) {
                if ($record) {
                    $useStatements[] = $currentUse;
                    $record = false;
                    $currentUse = [
                        'class' => '',
                        'as' => ''
                    ];
                }
                continue;
            }
            if ($token[0] === T_CLASS) {
                break;
            }
            if (strcasecmp($builtNamespace, $this->getNamespaceName()) === 0) {
                $matchedNamespace = true;
            }
            if ($matchedNamespace) {
                if ($token[0] === T_USE) {
                    $record = 'class';
                }
                if ($token[0] === T_AS) {
                    $record = 'as';
                }
                if ($record) {
                    switch ($token[0]) {
                        case T_STRING:
                        case T_NS_SEPARATOR:
                            if ($record) {
                                $currentUse[$record] .= $token[1];
                            }
                            break;
                    }
                }
            }
            if ($token[2] >= $this->getStartLine()) {
                break;
            }
        }
        foreach ($useStatements as &$useStatement) {
            if (empty($useStatement['as'])) {

                $useStatement['as'] = basename($useStatement['class']);
            }
        }
        return $useStatements;
    }

    private function readFileSource() {

        $file = fopen($this->getFileName(), 'r');
        $line = 0;
        $source = '';
        while (!feof($file)) {
            ++$line;
            if ($line >= $this->getStartLine()) {
                break;
            }
            $source .= fgets($file);
        }
        fclose($file);

        return $source;
    }
}
