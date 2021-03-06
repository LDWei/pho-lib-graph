<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Lib\Graph;

/**
 * Atomic graph entity, Edge
 * 
 * Edges (aka lines or arcs in graph theory) are used to
 * represent the relationships between Nodes of a Graph
 * therefore it is a fundamental unit of 
 * which graphs are formed.
 * 
 * Uses Observer Pattern to observe updates from its attribute bags,
 * as well as tail nodes.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Edge implements EntityInterface, EdgeInterface, \SplObserver {

    use EntityTrait {
        EntityTrait::__construct as onEntityLoad;
        EntityTrait::update as onEntityUpdate;
    }

    /**
     * Tail node. Where this originates from.
     *
     * @var TailNode
     */
    private $tail;
    
    /**
     * Head node. Where this is directed towards.
     *
     * @var HeadNode
     */
    private $head;

    /**
     * Predicate.
     *
     * @var PredicateInterface
     */
    private $predicate;

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $array = $this->baseToArray();
        $array["tail"] = (string) $this->tail->id();
        $array["head"] = (string) $this->head->id();
        $array["predicate"] = get_class($this->predicate);
        return $array;
    }


    /**
     * Constructor.
     *
     * @param NodeInterface $tail
     * @param PredicateInterface $predicate
     * @param NodeInterface $head
     */
    public function __construct(NodeInterface $tail, NodeInterface $head, ?PredicateInterface $predicate = null) 
    {
        $this->onEntityLoad();

        $this->head = new HeadNode();
        $this->head->set($head);

        $this->tail = new TailNode();
        $this->tail->set($tail);

        $this->head->edges()->addIncoming($this);
        $this->tail->edges()->addOutgoing($this);

        if(!is_null($predicate)) {
            $this->predicate = $predicate;
        }
        else {
            $predicate_class = get_class($this)."Predicate";
            if(class_exists($predicate_class)) {
                $reflector = new \ReflectionClass($predicate_class);
                if($reflector->implementsInterface(PredicateInterface::class)) {
                    $this->predicate = new $predicate_class;
                }
            }
            else {
                $this->predicate = new Predicate();
            }
        }
        
    }

    /**
     * {@inheritdoc}
     */
   public function head(): HeadNode
   {
    return $this->head;
   }

   /**
     * {@inheritdoc}
     */
   public function tail(): TailNode
   {
    return $this->tail;
   }


   /**
     * {@inheritdoc}
     */
   public function predicate(): PredicateInterface
   {
    return $this->predicate;
   }

   /**
     * {@inheritdoc}
     */
   public function orphan(): bool
   {
       // not implemented
       // edges set at construction.
       return false;
   }

   /**
    * Observed entities use this method to update the edge.
    *
    * @param \SplSubject $subject Updater.
    *
    * @return void
    */
   public function update(\SplSubject $subject): void
   {
       $this->onEntityUpdate($subject);
       if($subject instanceof AttributeBag) {
           $this->observeAttributeBagUpdate($subject);
       }
   }

   /**
    * Tail Nodes use this method to update about deletion
    *
    * @param \SplSubject $subject Updater.
    *
    * @return void
    */
   protected function observeTailNodeUpdate(\SplSubject $subject): void
   {
        if($this->predicate->binding()) {
            $this->head()->destroy();
        }
        $this->destroy();
   }


    
}