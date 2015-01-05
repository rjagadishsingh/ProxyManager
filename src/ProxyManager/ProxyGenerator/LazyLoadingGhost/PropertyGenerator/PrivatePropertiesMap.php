<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator;

use ProxyManager\Generator\Util\UniqueIdentifierGenerator;
use ProxyManager\ProxyGenerator\Util\Properties;
use ReflectionClass;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Property that contains the initializer for a lazy object
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class PrivatePropertiesMap extends PropertyGenerator
{
    const KEY_DEFAULT_VALUE = 'defaultValue';

    /**
     * Constructor
     *
     * @param ReflectionClass $originalClass
     * @param array           $excludedProperties
     */
    public function __construct(ReflectionClass $originalClass, array $excludedProperties = [])
    {
        parent::__construct(
            UniqueIdentifierGenerator::getIdentifier('privateProperties')
        );

        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setStatic(true);
        $this->setDocblock(
            '@var array[][] visibility and default value of defined properties, indexed by property name and class name'
        );
        $this->setDefaultValue($this->getMap($originalClass, $excludedProperties));
    }

    /**
     * @param ReflectionClass $originalClass
     *
     * @return int[][]|mixed[][]
     */
    private function getMap(ReflectionClass $originalClass)
    {
        $map = [];

        foreach (Properties::fromReflectionClass($originalClass)->getPrivateProperties() as $property) {
            $propertyKey = & $map[$property->getName()];

            $propertyKey[$property->getDeclaringClass()->getName()] = true;
        }

        return $map;
    }
}
