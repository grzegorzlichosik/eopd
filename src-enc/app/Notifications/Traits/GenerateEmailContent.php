<?php

namespace App\Notifications\Traits;

use App\Models\Encounter;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Spatie\IcalendarGenerator\Components\Calendar as SpatieCalendar;
use Spatie\IcalendarGenerator\Components\Event as SpatieEvent;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\TextProperty;

trait GenerateEmailContent
{

    /**
     * @codeCoverageIgnore
     */
    private function getEventDetails(Encounter $encounter, string $date, string $start, string $end): HtmlString
    {
        return new HtmlString(
            '<table border = "0">
                        <tr>
                            <td>' . trans('mail.scheduled_date') . '</td>
                            <td>:</td>
                            <td> ' . $date . '</td>
                        </tr>
                        <tr>
                            <td>' . trans('mail.start_time') . '</td>
                            <td>:</td>
                            <td> ' . $start . '</td>
                        </tr>
                        <tr>
                            <td>' . trans('mail.end_time') . '</td>
                            <td>:</td>
                            <td> ' . $end . '</td>
                        </tr>
                        <tr>
                            <td>' . trans('mail.channel') . '</td>
                            <td>:</td>
                            <td> ' . $encounter->channel?->type?->name . '</td>
                        </tr>
                        <tr>
                            <td>' . trans('mail.max_participants') . '</td>
                            <td>:</td>
                            <td> ' . $encounter->channel?->max_participants . '</td>
                        </tr>
                    </table>
                    <br> '
        );
    }

    /**
     * @codeCoverageIgnore
     */
    private function getRequestorDetails(Encounter $encounter): HtmlString
    {
        $requestor = $encounter->attendees->where('is_original', 1)->first();

        $result = '<table>
                      <tr>
                          <td>' . trans('mail.name') . '</td>
                          <td>:</td>
                          <td>' . $requestor->name . '</td>
                      </tr>
                      <tr>
                          <td>' . trans('mail.email') . '</td>
                          <td>:</td>
                          <td>' . $requestor->email . '</td>
                      </tr>';

        if ($requestor->phone_number) {
            $result .= '
                <tr>
                  <td>' . trans('mail.phone_number') . '</td>
                  <td>:</td>
                  <td>' . $requestor->phone_number . '</td>
                </tr>
            ';
        }

        $result .= '</table><br>';

        return new HtmlString($result);
    }

    /**
     * @codeCoverageIgnore
     */
    private function getLinks(Encounter $encounter): HtmlString
    {
        $cancelLink = url(self::$url . $encounter->uuid . "/cancel");
        $rescheduleLink = url(self::$url . $encounter->uuid . "/reschedule");
        $addParticipantsLink = url(self::$url . $encounter->uuid . "/add_participants");

        $result = '<table>
                      <tr>
                          <td>
                              <a
                                  class="button button-primary"
                                  style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont,
                                  Roboto, Helvetica, Arial, sans-serif; position: relative;
                                  -webkit-text-size-adjust: none; border-radius: 4px;
                                  color: #fff; display: inline-block; overflow: hidden;
                                  text-decoration: none; background-color: #2d3748;
                                  border-bottom: 8px solid #2d3748; border-left: 18px solid #2d3748;
                                  border-right: 18px solid #2d3748; border-top: 8px solid #2d3748;"
                                  href="' . $rescheduleLink . '"
                              >
                              ' . trans('mail.reschedule_booking') . '
                              </a>
                          </td>
                          <td>&nbsp;</td>
                          <td>
                              <a
                                  class="button button-primary"
                                  style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont,
                                  Roboto, Helvetica, Arial, sans-serif; position: relative;
                                  -webkit-text-size-adjust: none; border-radius: 4px;
                                  color: #fff; display: inline-block; overflow: hidden;
                                  text-decoration: none; background-color: #2d3748;
                                  border-bottom: 8px solid #2d3748; border-left: 18px solid #2d3748;
                                  border-right: 18px solid #2d3748; border-top: 8px solid #2d3748;"
                                  href="' . $cancelLink . '"
                              >
                              ' . trans('mail.cancel_booking') . '
                              </a>
                          </td>';


        if ($encounter->attendees_count < $encounter->channel->max_participants) {
            $result .= '<td>&nbsp;</td>
                      <td>
                          <a
                              class="button button-primary"
                              style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont,
                                  Roboto, Helvetica, Arial, sans-serif; position: relative;
                                  -webkit-text-size-adjust: none; border-radius: 4px;
                                  color: #fff; display: inline-block; overflow: hidden;
                                  text-decoration: none; background-color: #2d3748;
                                  border-bottom: 8px solid #2d3748; border-left: 18px solid #2d3748;
                                  border-right: 18px solid #2d3748; border-top: 8px solid #2d3748;"
                              href="' . $addParticipantsLink . '"
                          >
                          ' . trans('mail.add_participants') . '
                          </a>
                      </td>';
        }


        $result .= '</tr></table><br>';
        return new HtmlString($result);
    }

}
