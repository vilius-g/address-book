<?php

namespace App\Serializer;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\DataTransformer\PhoneDataTransformer;
use App\Entity\Contact;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use function array_key_exists;
use function is_array;

/**
 * Implements field normalization for Contact entities.
 */
class ContactAttributeNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface, ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use DenormalizerAwareTrait;
    private const ALREADY_CALLED = self::class.'::ALREADY_CALLED';

    /**
     * @var PhoneDataTransformer
     */
    private $phoneDataTransformer;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var IriConverterInterface
     */
    private $iriConverter;

    public function __construct(
        PhoneDataTransformer $phoneDataTransformer,
        Security $security,
        IriConverterInterface $iriConverter
    ) {
        $this->phoneDataTransformer = $phoneDataTransformer;
        $this->security = $security;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if ($this->hasBeenCalledPreviously($context)) {
            return false;
        }

        return $data instanceof Contact;
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $this->normalizePhone($object);
        $context[self::ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        if ($this->hasBeenCalledPreviously($context)) {
            return false;
        }

        return Contact::class === $type && is_array($data);
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $data = $this->formatPhone($data);
        $data = $this->fillMissingOwner($data, $context);

        $context[self::ALREADY_CALLED] = true;

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * Format phone number for display.
     *
     * @param $object
     */
    private function normalizePhone(Contact $object): void
    {
        $object->setPhone($this->phoneDataTransformer->reverseTransform($object->getPhone()));
    }

    /**
     * Format provided phone number for storage using consistent format.
     *
     * @param array $data
     * @return array
     */
    private function formatPhone(array $data): array
    {
        if (array_key_exists('phone', $data)) {
            $data['phone'] = $this->phoneDataTransformer->transform($data['phone']);
        }

        return $data;
    }

    /**
     * Fill owner information from currently authenticated user when it is not provided.
     *
     * @param $data
     * @param array $context
     * @return array
     */
    private function fillMissingOwner(array $data, array $context): array
    {
        if (!array_key_exists('owner', $data) && $this->isItemCreationOperation($context)) {
            $data['owner'] = $this->iriConverter->getIriFromItem($this->security->getUser());
        }

        return $data;
    }

    /**
     * Return true if this normalizer has been already called in previous iteration.
     *
     * @param array $context
     * @return bool
     */
    private function hasBeenCalledPreviously(array $context): bool
    {
        return isset($context[self::ALREADY_CALLED]);
    }

    /**
     * Is current API operation creating a new item.
     *
     * @param array $context
     * @return bool
     */
    private function isItemCreationOperation(array $context): bool
    {
        return ('collection' === $context['operation_type'] && 'post' === $context['collection_operation_name']) ||
            ('item' === $context['operation_type'] && 'put' === $context['item_operation_name']);
    }
}
