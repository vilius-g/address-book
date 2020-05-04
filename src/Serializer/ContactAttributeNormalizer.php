<?php


namespace App\Serializer;


use App\DataTransformer\PhoneDataTransformer;
use App\Entity\Contact;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

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
     * ContactNormalizer constructor.
     * @param PhoneDataTransformer $phoneDataTransformer
     */
    public function __construct(PhoneDataTransformer $phoneDataTransformer)
    {
        $this->phoneDataTransformer = $phoneDataTransformer;
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
        // Normalize phone format for storage.
        $data['phone'] = $this->phoneDataTransformer->transform($data['phone']);
        $context[self::ALREADY_CALLED] = true;

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }
}
