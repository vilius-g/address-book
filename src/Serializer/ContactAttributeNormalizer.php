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
        // Make sure we're not called twice
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Contact;
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        // Format phone number for display.
        $object->setPhone($this->phoneDataTransformer->reverseTransform($object->getPhone()));
        $context[self::ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        // Make sure we're not called twice
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return Contact::class === $type;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (array_key_exists('phone', $data)) {
            // Normalize phone format for storage.
            $data['phone'] = $this->phoneDataTransformer->transform($data['phone']);
        }

        if (!array_key_exists('owner', $data) && $this->isItemCreationOperation($context)) {
            // Fill-in missing owner from session.
            $data['owner'] = $this->iriConverter->getIriFromItem($this->security->getUser());
        }

        // Do not execute again.
        $context[self::ALREADY_CALLED] = true;

        return $this->denormalizer->denormalize($data, $type, $format, $context);
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
