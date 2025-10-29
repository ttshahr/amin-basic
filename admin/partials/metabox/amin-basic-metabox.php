<?php
$code = get_post_meta( $post->ID, 'abpCode', true );
$inBox = get_post_meta( $post->ID, 'abpInBox', true );
$recordId = get_post_meta( $post->ID, 'abpRecordId', true );
$typeShow = get_post_meta( $post->ID, 'abpTypeShow', true );
$unitName = get_post_meta( $post->ID, 'abpUnitName', true );
?>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
                <label for="abpCode">
                    کد محصول:
                </label>
            </th>
            <td>
                <input type="text" readonly id="abpCode" name="abpCode" value="<?php echo absint( $code ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="abpInbox">
                    تعداد در بسته:
                </label>
            </th>
            <td>
                <input type="text" readonly id="abpInbox" name="abpInbox" value="<?php echo absint( $inbox ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="abpRecordId">
                    رکورد آی دی:
                </label>
            </th>
            <td>
                <input type="text" readonly id="abpRecordId" name="abpRecordId" value="<?php echo $recordId; ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="abpTypeShow">
                    نمایش:
                </label>
            </th>
            <td>
                <input type="text" readonly id="abpTypeShow" name="abpTypeShow" value="<?php echo absint( $typeShow ); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="abpUnitName">
                    واحد:
                </label>
            </th>
            <td>
                <input type="text" readonly id="abpUnitName" name="abpUnitName" value="<?php echo $unitName; ?>">
            </td>
        </tr>
    </tbody>
</table>